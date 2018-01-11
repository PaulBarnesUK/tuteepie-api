<?php

namespace App\Http\Middleware;

use App\Http\Controllers\JsonableTrait;
use Closure;
use Illuminate\Support\Facades\Config;

class RejectIfNotSelf
{
    use JsonableTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user()->id !== $request->tutor->user->id) {
            return $this->errorResponse(403, config('constants.response_titles.FORBIDDEN'));
        }

        return $next($request);

    }
}
