<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class RecaptchaHelper
{
    public static function verify($response)
    {
        $secretKey = config('services.recaptcha.secret_key');

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secretKey,
            'response' => $response,
            'remoteip' => request()->ip()
        ]);

        return $response->json();
    }
}
