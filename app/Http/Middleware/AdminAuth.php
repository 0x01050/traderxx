<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class AdminAuth extends Middleware
{
  public function handle($request, \Closure $next)
  {
    $user = auth()->user();
    if($user->type != 'administrator') {
      throw new AuthenticationException(
        'Unauthenticated.', [null], $this->redirectTo($request)
      );
    }
    return $next($request);
  }
}
