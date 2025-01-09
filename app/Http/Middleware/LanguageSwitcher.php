<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class LanguageSwitcher
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Obtém o idioma na query string ou na sessão
        $locale = $request->get('lang', Session::get('lang', config('app.locale')));

        if (in_array($locale, ['en', 'pt_BR', 'es'])) {
            App::setLocale($locale); // Define o idioma da aplicação
            Session::put('lang', $locale); // Armazena na sessão
        }

        return $next($request);
    }
}
