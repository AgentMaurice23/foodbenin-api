<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OwnsRestaurant
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(
        Request $request,
        Closure $next
    )
    {
        $restaurant =
            $request
                ->route(
                    'restaurant'
                );

        if (
            $restaurant->owner_id
            !== auth()->id()
        ) {
            abort(403);
        }

        return $next(
            $request
        );
    }
}
