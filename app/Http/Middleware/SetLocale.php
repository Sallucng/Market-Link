<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Supported application locales.
     */
    public const SUPPORTED_LOCALES = [
        'en' => ['name' => 'English', 'native' => 'English', 'flag' => '🇺🇸', 'dir' => 'ltr'],
        'ur' => ['name' => 'Urdu', 'native' => 'اردو', 'flag' => '🇵🇰', 'dir' => 'rtl'],
        'es' => ['name' => 'Spanish', 'native' => 'Español', 'flag' => '🇪🇸', 'dir' => 'ltr'],
        'ar' => ['name' => 'Arabic', 'native' => 'العربية', 'flag' => '🇸🇦', 'dir' => 'rtl'],
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale') 
            ?? $request->cookie('marketlink_locale') 
            ?? $request->cookie('locale')
            ?? (auth()->check() ? (method_exists(auth()->user(), 'getPreference') ? auth()->user()->getPreference('locale') : null) ?? (auth()->user()->preferred_language ?? null) : null)
            ?? config('app.locale', 'en');

        if (!array_key_exists($locale, self::SUPPORTED_LOCALES)) {
            $locale = 'en';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
