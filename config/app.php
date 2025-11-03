<?php

use App\Constants\Faq;

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | This value is the name of your application, which will be used when the
    | framework needs to place the application's name in a notification or
    | other UI elements where an application name needs to be displayed.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | This value determines the "environment" your application is currently
    | running in. This may determine how you prefer to configure various
    | services the application utilizes. Set this in your ".env" file.
    |
    */

    'env' => env('APP_ENV', 'production'),

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | When your application is in debug mode, detailed error messages with
    | stack traces will be shown on every error that occurs within your
    | application. If disabled, a simple generic error page is shown.
    |
    */

    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | This URL is used by the console to properly generate URLs when using
    | the Artisan command line tool. You should set this to the root of
    | the application so that it's available within Artisan commands.
    |
    */

    'url' => env('APP_URL', 'http://localhost'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default timezone for your application, which
    | will be used by the PHP date and date-time functions. The timezone
    | is set to "UTC" by default as it is suitable for most use cases.
    |
    */

    'timezone' => 'UTC',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | The application locale determines the default locale that will be used
    | by Laravel's translation / localization methods. This option can be
    | set to any locale for which you plan to have translation strings.
    |
    */

    'locale' => env('APP_LOCALE', 'en'),

    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),

    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | This key is utilized by Laravel's encryption services and should be set
    | to a random, 32 character string to ensure that all encrypted values
    | are secure. You should do this prior to deploying the application.
    |
    */

    'cipher' => 'AES-256-CBC',

    'key' => env('APP_KEY'),

    'previous_keys' => [
        ...array_filter(
            explode(',', (string) env('APP_PREVIOUS_KEYS', ''))
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    |
    | These configuration options determine the driver used to determine and
    | manage Laravel's "maintenance mode" status. The "cache" driver will
    | allow maintenance mode to be controlled across multiple machines.
    |
    | Supported drivers: "file", "cache"
    |
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store'  => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    'prism' => [
        'system_prompts' => [
            'intend_detection'              => env('PRISM_INTEND_DETECTION_SYSTEM_PROMPT', "You are an intent classifier. Outputs allowed: exactly one token — 'order', 'faq', or null.\n If message contains order indicators or an order id, output 'order'.\n If message matches any FAQ keyword from the provided map, output 'faq'.\n Faq related keywards are : ".json_encode(Faq::TAGS)."\n Otherwise output null.\n Return only the token."),
            'advanced_faq_intent_detection' => env('PRISM_ADVANCED_FAQ_INTENT_DETECTION_SYSTEM_PROMPT', "You are a customer query intent classifier for an e-commerce chatbot. Your task: Given a user message, identify the most relevant tag value from the provided FAQ keyword map. Rules: 1. The map below contains multiple keywords (keys) mapped to their corresponding intent values. 2. You must find which key(s) the message best matches — based on meaning or similarity. 3. Output only the corresponding mapped value (from the map below) that best fits the user's intent. 4. If no relevant match is found, return null (exactly this word). 5. Always return only one word or phrase — the mapped value from the map (no explanations, no extra text). FAQ Keyword Map: ".json_encode(Faq::TAGS, JSON_PRETTY_PRINT)." Example: - Message: 'Where is my order?' Output: 'delivery tracking' - Message: 'Change delivery date please' Output: 'update delivery' - Message: 'My payment failed' Output: 'payment failed' - Message: 'How do I apply a promo code?' Output: 'coupon' - Message: 'Hi' → Output: null"),
        ],
    ],

];
