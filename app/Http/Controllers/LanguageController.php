<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switchLanguage($language)
    {
        // Validate that the language is supported
        $supportedLanguages = ['en', 'id'];

        if (!in_array($language, $supportedLanguages)) {
            return redirect()->back();
        }

        // Store in session
        session(['locale' => $language]);

        // Set the application locale immediately
        app()->setLocale($language);

        // Redirect back with cookie
        return redirect()->back()
            ->cookie('language', $language, 525600); // 1 year cookie
    }
}
