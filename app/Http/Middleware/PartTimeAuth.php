<?php

namespace App\Http\Middleware;

// namespace App\Http\Controllers\Auth;

use Closure;

class PartTimeAuth
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if (auth()->check() && auth()->user()->retirement_date != null) {
			auth()->logout();

			return redirect(route('login'))->withErrors([
				'user_id' => 'このユーザーは退職されています。'
			]);
		}

		if (auth()->check() && auth()->user()->roles != '4') {
			return $next($request);
		}

		return redirect()->route('salary.edit', ['id' => auth()->user()->id, 'date' => date('Y-m-d')]);
	}
}
