<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class LocaleController extends Controller
{
    /**
     * Switch application language and redirect back.
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (!array_key_exists($locale, SetLocale::SUPPORTED_LOCALES)) {
            $locale = 'en';
        }

        session(['locale' => $locale]);
        App::setLocale($locale);

        if (Auth::check()) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            if (method_exists($user, 'setPreference')) {
                $user->setPreference('locale', $locale);
            }
        }

        $langInfo = SetLocale::SUPPORTED_LOCALES[$locale];

        return back()
            ->withCookie(cookie()->forever('marketlink_locale', $locale))
            ->withCookie(cookie()->forever('locale', $locale))
            ->with('success', "Language switched to {$langInfo['native']} successfully.");
    }
}
