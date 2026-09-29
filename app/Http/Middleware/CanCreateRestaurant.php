<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CanCreateRestaurant
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
        $count =
            auth()
                ->user()
                ->restaurants()
                ->count();

        if ($count >= 10) {

            abort(
                403,
                'Limite atteinte.'
            );
        }

        return $next(
            $request
        );
    }
}
