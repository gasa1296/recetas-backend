<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleMiddleware
{
    /**
     * Supported application locales.
     *
     * @var list<string>
     */
    protected array $supportedLocales = ['en', 'es'];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('X-Locale')
            ?? $request->query('locale')
            ?? $request->query('lang');

        if (! $locale || ! in_array($locale, $this->supportedLocales, true)) {
            $preferred = $request->getPreferredLanguage($this->supportedLocales);
            $locale = $preferred ?: config('app.locale', 'en');
        }

        if (in_array($locale, $this->supportedLocales, true)) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
