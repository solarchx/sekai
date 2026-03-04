<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switchLanguage(Request $request, $language)
    {
        // Validate that the language is supported
        if (in_array($language, ['en', 'id'])) {
            session(['locale' => $language]);
            app()->setLocale($language);
        }

        return redirect()->back();
    }
}
