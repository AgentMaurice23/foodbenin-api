<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController; 
use App\Http\Controllers\Restaurant\RestaurantDashboardController; 

use App\Livewire\Restaurant\CreateRestaurant;
use App\Livewire\Restaurant\RestaurantDashboard;
use App\Livewire\Dashboard\DeliveryDashboard;

use App\Livewire\Restaurant\CategoryList;
use App\Livewire\Restaurant\ExtraList;
use App\Livewire\Restaurant\CreateExtra;
use App\Livewire\Restaurant\EditExtra;
use App\Livewire\Restaurant\EditProduct;

use App\Livewire\Restaurant\ProductList;
use App\Livewire\Restaurant\CreateProduct;

use App\Livewire\Restaurant\CreateCategory;



Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});



// Routes super admin 
Route::middleware([
    'auth',
    'role:super_admin'
])
->prefix('admin')
->group(function () {

    Route::get(
        '/dashboard',
        [AdminDashboardController::class,'index']
    )
    ->name('admin.dashboard');


    Route::view(
        '/restaurants',
        'admin.restaurants.index'
    )
    ->name(
        'admin.restaurants'
    );
    
    Route::get(
    '/{restaurant}/extras',
        ExtraList::class
    )->name(
        'extras'
    );

    Route::get(
        '/{restaurant}/extras/create',
        CreateExtra::class
    )->name(
        'extras.create'
    );

    Route::get(
        '/{restaurant}/extras/{extra}/edit',
        EditExtra::class
    )->name(
        'extras.edit'
    );

    Route::get(
        '/{restaurant}/products',
        ProductList::class
    )->name(
        'products'
    );

    Route::get(
        '/{restaurant}/products/create',
        CreateProduct::class
    )->name(
        'products.create'
    );

    Route::get(
        '/{restaurant}/products/{product}/edit',
        EditProduct::class
    )->name(
        'products.edit'
    );
});


// creation de restaurant 

Route::middleware([
    'auth',
    'verified'
])
->prefix('restaurant')
->name('restaurant.')
->group(function () {

    Route::get(
        '/create',
        CreateRestaurant::class)
    ->name('create');

    Route::get(
        '/{restaurant}/dashboard',
        RestaurantDashboard::class)
    ->middleware(
        'owns.restaurant')
    ->name(
        'dashboard');

        Route::get(
            '/{restaurant}/categories',
            CategoryList::class
        )->name(
            'categories'
        );

        Route::get(
            '/{restaurant}/categories/create',
            CreateCategory::class
        )->name(
            'categories.create'
        );
});


Route::middleware([
    'auth',
    'restaurant'
])->group(function () {

});



Route::middleware([
    'auth',
    'driver'
])->group(function () {

});


// Tout utilisateur connecté
Route::middleware(['auth'])
    ->prefix('dashboard')
    ->group(function () {

});

// Propriétaire de restaurant
Route::middleware([
    'auth',
    'role:owner'
])
    ->prefix('owner')
    ->group(function () {

});

// Manager
Route::middleware([
    'auth',
    'role:manager'
])
    ->prefix('manager')
    ->group(function () {

});

// Admin plateforme
Route::middleware([
    'auth',
    'role:admin'
])
    ->prefix('admin')
    ->group(function () {

});

Route::middleware([
'auth',
'verified',
])
->prefix('dashboard/delivery')
->name('delivery.')
->group(function () {

    Route::get('/', DeliveryDashboard::class)
        ->name('dashboard');
});