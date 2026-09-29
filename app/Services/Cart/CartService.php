<?php

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\Extra;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Enums\ProductStockEnum;
use Exception;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CartService
{

    public function getOrCreateCart(
        User $user,
        int $restaurantId
    ): Cart {

        return Cart::firstOrCreate(

            [

                'user_id' =>
                    $user->id,

                'restaurant_id' =>
                    $restaurantId,
            ],

            [

                'uuid' =>
                    Str::uuid(),
            ]
        );
    }

   public function addProduct(
    User $user,
    Product $product,
    ?ProductVariant $variant = null,
    array $extraIds = [],
    int $quantity = 1
    ): Cart {

        return DB::transaction(

            function () use (
                $user,
                $product,
                $variant,
                $extraIds,
                $quantity
            ) {

                /*
                |--------------------------------------------------------------------------
                | Récupération du panier
                |--------------------------------------------------------------------------
                */

                $cart = $this->getOrCreateCart(
                    $user,
                    $product->restaurant_id
                );

                /*
                |--------------------------------------------------------------------------
                | Chargement des extras
                |--------------------------------------------------------------------------
                */

                $extras = Extra::whereIn(
                    'id',
                    $extraIds
                )->get();

                /*
                |--------------------------------------------------------------------------
                | Snapshot des extras
                |--------------------------------------------------------------------------
                */

                $extrasSnapshot = $extras
                    ->map(
                        fn ($extra) => [

                            'id' => $extra->id,

                            'name' => $extra->name,

                            'price' => $extra->price,
                        ]
                    )
                    ->values()
                    ->toArray();

                /*
                |--------------------------------------------------------------------------
                | Calcul du prix
                |--------------------------------------------------------------------------
                */

                $unitPrice = $product->calculatePrice(
                    $variant,
                    $extras
                );

                /*
                |--------------------------------------------------------------------------
                | Recherche d'une ligne existante
                |--------------------------------------------------------------------------
                */

                $existingItem = $cart
                    ->items()
                    ->get()
                    ->first(

                        function ($item)
                        use (
                            $product,
                            $variant,
                            $extrasSnapshot
                        ) {

                            return

                                $item->product_id
                                ===
                                $product->id

                                &&

                                $item->product_variant_id
                                ===
                                ($variant?->id)

                                &&

                                collect(
                                    $item->extras
                                )
                                ->sortBy('id')
                                ->values()
                                ->toArray()

                                ===

                                collect(
                                    $extrasSnapshot
                                )
                                ->sortBy('id')
                                ->values()
                                ->toArray();
                        }
                    );

                /*
                |--------------------------------------------------------------------------
                | Quantité finale
                |--------------------------------------------------------------------------
                */

                $newQuantity =

                    $existingItem
                        ? $existingItem->quantity + $quantity
                        : $quantity;

                /*
                |--------------------------------------------------------------------------
                | Validation stock
                |--------------------------------------------------------------------------
                */

                $this->validateStock(

                    $product,

                    $newQuantity
                );

                /*
                |--------------------------------------------------------------------------
                | Fusion ligne existante
                |--------------------------------------------------------------------------
                */

                if ($existingItem) {

                    $existingItem->update([

                        'quantity' =>
                            $newQuantity,

                        'total_price' =>

                            $existingItem->unit_price
                            *
                            $newQuantity,
                    ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Nouvelle ligne
                |--------------------------------------------------------------------------
                */

                else {

                    $cart
                        ->items()
                        ->create([

                            'uuid' =>
                                Str::uuid(),

                            'product_id' =>
                                $product->id,

                            'product_variant_id' =>
                                $variant?->id,

                            'quantity' =>
                                $quantity,

                            'unit_price' =>
                                $unitPrice,

                            'total_price' =>
                                $unitPrice
                                *
                                $quantity,

                            'extras' =>
                                $extrasSnapshot,
                        ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Recalcul panier
                |--------------------------------------------------------------------------
                */

                $this->calculateCart(
                    $cart
                );

                /*
                |--------------------------------------------------------------------------
                | Retour
                |--------------------------------------------------------------------------
                */

                return $cart
                    ->fresh(
                        'items'
                    );
            }
        );
    }
    public function calculateCart(Cart $cart)
    {
        $subtotal =
            $cart
                ->items()
                ->sum(
                    'total_price'
                );

        $delivery =
            $cart
                ->restaurant
                ->delivery_fee;

        $discount =
            $cart
                ->discount;

        $cart->update([

            'subtotal' =>
                $subtotal,

            'delivery_fee' =>
                $delivery,

            'total' =>
                $subtotal
                +
                $delivery
                -
                $discount,
        ]);

        return $cart;
    }

    public function validateStock(
        Product $product,
        int $quantity)
    {
        if (
            $product->stock_type
            ===
            ProductStockEnum::OUT_OF_STOCK
        ) {

            throw new Exception(
                'Produit en rupture.'
            );
        }
        if (
        $product->stock_type
            ===
            ProductStockEnum::UNLIMITED
        ) {

            return true;
        }

        if (
            $product->stock_type
            ===
            ProductStockEnum::LIMITED
        ) {

            if (
                $product->stock_quantity
                <
                $quantity
            ) {

                throw new Exception(

                    'Stock insuffisant.'
                );
            }
        }
            return true;
    }
    public function updateQuantity($item,int $quantity)
    {
        $item->update([

            'quantity' =>
                $quantity,

            'total_price' =>
                $item->unit_price
                *
                $quantity,
        ]);

        return
            $this
                ->calculateCart(
                    $item->cart
                );
    }

    public function removeItem(
        $item
    )
    {
        $cart =
            $item->cart;

        $item->delete();

        return
            $this
                ->calculateCart(
                    $cart
                );
    }

    public function clearCart(
        Cart $cart
    )
    {
        $cart
            ->items()
            ->delete();

        $cart->update([

            'subtotal' => 0,

            'discount' => 0,

            'total' => 0,
        ]);
    }

    public function applyCoupon(
        Cart $cart,
        string $code
    ): Cart {

        /*
        |--------------------------------------------------------------------------
        | Recherche du coupon
        |--------------------------------------------------------------------------
        */

        $coupon = Coupon::where(
            'code',
            strtoupper($code)
        )->first();

        if (!$coupon) {

            throw new Exception(
                'Coupon invalide.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Coupon actif
        |--------------------------------------------------------------------------
        */

        if (!$coupon->is_active) {

            throw new Exception(
                'Coupon désactivé.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date de début
        |--------------------------------------------------------------------------
        */

        if (
            $coupon->starts_at
            &&
            now()->lt(
                $coupon->starts_at
            )
        ) {

            throw new Exception(
                'Coupon non disponible.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date expiration
        |--------------------------------------------------------------------------
        */

        if (
            $coupon->expires_at
            &&
            now()->gt(
                $coupon->expires_at
            )
        ) {

            throw new Exception(
                'Coupon expiré.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Limite d'utilisation
        |--------------------------------------------------------------------------
        */

        if (

            $coupon->usage_limit

            &&

            $coupon->used_count
            >=
            $coupon->usage_limit

        ) {

            throw new Exception(
                'Coupon épuisé.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Coupon restaurant
        |--------------------------------------------------------------------------
        */

        if (

            $coupon->restaurant_id

            &&

            $coupon->restaurant_id
            !=
            $cart->restaurant_id

        ) {

            throw new Exception(
                'Coupon invalide.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Montant minimum
        |--------------------------------------------------------------------------
        */

        if (

            $cart->subtotal

            <

            $coupon->minimum_amount

        ) {

            throw new Exception(
                'Montant minimum non atteint.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Calcul remise
        |--------------------------------------------------------------------------
        */

        if (
            $coupon->type
            ===
            CouponTypeEnum::FIXED
        ) {

            $discount =
                $coupon->value;
        }

        else {

            $discount = (

                $cart->subtotal
                *
                $coupon->value

            ) / 100;
        }

        /*
        |--------------------------------------------------------------------------
        | Protection
        |--------------------------------------------------------------------------
        */

        $discount = min(
            $discount,
            $cart->subtotal
        );

        /*
        |--------------------------------------------------------------------------
        | Mise à jour panier
        |--------------------------------------------------------------------------
        */

        $cart->update([

            'discount' =>
                $discount,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Recalcul
        |--------------------------------------------------------------------------
        */

        $this->calculateCart(
            $cart
        );

        /*
        |--------------------------------------------------------------------------
        | Retour
        |--------------------------------------------------------------------------
        */

        return $cart->fresh();
    }

    public function checkout(
        Cart $cart,
        array $data
    ): Order {

        return DB::transaction(

            function () use (
                $cart,
                $data
            ) {

                /*
                |--------------------------------------------------------------------------
                | Validation panier vide
                |--------------------------------------------------------------------------
                */

                if (
                    $cart
                        ->items()
                        ->count() === 0
                ) {

                    throw new Exception(
                        'Panier vide.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Validation finale des stocks
                |--------------------------------------------------------------------------
                */

                foreach (
                    $cart->items
                    as $item
                ) {

                    $this->validateStock(

                        $item->product,

                        $item->quantity
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | Création de la commande
                |--------------------------------------------------------------------------
                */

                $order = Order::create([

                    'uuid' => Str::uuid(),

                    'user_id' =>
                        $cart->user_id,

                    'restaurant_id' =>
                        $cart->restaurant_id,

                    'subtotal' =>
                        $cart->subtotal,

                    'delivery_fee' =>
                        $cart->delivery_fee,

                    'discount' =>
                        $cart->discount,

                    'total' =>
                        $cart->total,

                    'status' =>
                        OrderStatusEnum::PENDING,

                    'payment_status' =>
                        PaymentStatusEnum::PENDING,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Création des lignes de commande
                |--------------------------------------------------------------------------
                */

                foreach (
                    $cart->items
                    as $item
                ) {

                    $order
                        ->items()
                        ->create([

                            'product_id' =>
                                $item->product_id,

                            'product_name' =>
                                $item
                                    ->product
                                    ->name,

                            'unit_price' =>
                                $item
                                    ->unit_price,

                            'quantity' =>
                                $item
                                    ->quantity,

                            'total' =>
                                $item
                                    ->total_price,

                            'variant' =>

                                $item->variant

                                ? [

                                    'id' =>
                                        $item
                                            ->variant
                                            ->id,

                                    'name' =>
                                        $item
                                            ->variant
                                            ->name,

                                    'price' =>
                                        $item
                                            ->variant
                                            ->price,
                                ]

                                : null,

                            'extras' =>
                                $item
                                    ->extras,
                        ]);
                }

                /*
                |--------------------------------------------------------------------------
                | Décrémentation du stock
                |--------------------------------------------------------------------------
                */

                foreach (
                    $cart->items
                    as $item
                ) {

                    if (

                        $item
                            ->product
                            ->stock_type

                        ===

                        ProductStockEnum::LIMITED

                    ) {

                        $item
                            ->product
                            ->decrement(

                                'stock_quantity',

                                $item
                                    ->quantity
                            );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Consommation du coupon
                |--------------------------------------------------------------------------
                */

                if (

                    $cart->discount > 0

                    &&

                    isset(
                        $data['coupon']
                    )

                ) {

                    $coupon =
                        Coupon::where(
                            'code',
                            strtoupper(
                                $data['coupon']
                            )
                        )
                        ->first();

                    if ($coupon) {

                        $coupon
                            ->increment(
                                'used_count'
                            );
                    }
                }

                /*
                |--------------------------------------------------------------------------
                | Création du paiement
                |--------------------------------------------------------------------------
                */

                Payment::create([

                    'uuid' =>
                        Str::uuid(),

                    'order_id' =>
                        $order->id,

                    'amount' =>
                        $order->total,

                    'provider' =>
                        $data['provider'],

                    'status' =>
                        PaymentStatusEnum::PENDING,
                ]);

                /*
                |--------------------------------------------------------------------------
                | Nettoyage du panier
                |--------------------------------------------------------------------------
                */

                $cart
                    ->items()
                    ->delete();

                $cart
                    ->delete();

                /*
                |--------------------------------------------------------------------------
                | Retour
                |--------------------------------------------------------------------------
                */

                return $order->fresh(
                    'items'
                );
            }
        );
    }
    
}