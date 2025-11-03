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
            'intend_detection'                => env('PRISM_INTEND_DETECTION_SYSTEM_PROMPT', "You are an intent classifier. Outputs allowed: exactly one token — 'order', 'faq', or null.\n If message contains order indicators or an order id, output 'order'.\n If message matches any FAQ keyword from the provided map, output 'faq'.\n Faq related keywards are : ".json_encode(Faq::TAGS)."\n Otherwise output null.\n Return only the token."),
            'advanced_faq_intent_detection'   => env('PRISM_ADVANCED_FAQ_INTENT_DETECTION_SYSTEM_PROMPT', "You are a customer query intent classifier for an e-commerce chatbot. Your task: Given a user message, identify the most relevant tag value from the provided FAQ keyword map. Rules: 1. The map below contains multiple keywords (keys) mapped to their corresponding intent values. 2. You must find which key(s) the message best matches — based on meaning or similarity. 3. Output only the corresponding mapped value (from the map below) that best fits the user's intent. 4. If no relevant match is found, return null (exactly this word). 5. Always return only one word or phrase — the mapped value from the map (no explanations, no extra text). FAQ Keyword Map: ".json_encode(Faq::TAGS, JSON_PRETTY_PRINT)." Example: - Message: 'Where is my order?' Output: 'delivery tracking' - Message: 'Change delivery date please' Output: 'update delivery' - Message: 'My payment failed' Output: 'payment failed' - Message: 'How do I apply a promo code?' Output: 'coupon' - Message: 'Hi' → Output: null"),
            'advanced_order_intent_detection' => env('PRISM_ADVANCED_ORDER_INTENT_DETECTION_SYSTEM_PROMPT', "You are an intent classification engine for an e-commerce chatbot. Your job: Given a user's message, identify the correct intent code exactly as defined below. You must follow these rules strictly and return only one of the given values. Rules: 1. Read the user message carefully and infer its meaning. 2. Match it to one of the intents below based on the provided examples and logic. 3. If no intent applies, return \"unknown\". 4. Output only the intent code (no text, no explanation, no quotes). Intent Map: - If the message is about canceling an order or purchase, e.g. 'cancel my order', 'please cancel the purchase' → order.cancel - If the message is about tracking an order location, e.g. 'where is my order', 'track my package', 'shipment tracking', 'where’s my parcel' → order.track_location - If the message is about refunds or returns, e.g. 'I want a refund', 'return the product', 'money back', 'initiate refund' → order.request_refund - If the message is asking for order status, e.g. 'order status', 'what is the status of my order' → order.status - If the message asks for estimated delivery date or time, e.g. 'when will my order arrive', 'expected delivery date', 'when will it be delivered', 'delivery time' → order.estimate_delivery - If the message confirms or updates delivery completion, e.g. 'order delivered', 'mark as delivered', 'I received my parcel', 'delivery confirmation' → order.update_delivered - If the message asks for payment status, e.g. 'is my payment received', 'payment pending', 'payment successful?', 'paid or unpaid' → order.payment_status - If the message refers to payment method, e.g. 'how did I pay', 'pay using wallet', 'change payment method', 'use card' → order.payment_method - If the message asks about amount, cost, invoice, discount, or total price, e.g. 'total amount', 'final price', 'invoice please', 'how much I paid' → order.details_amount - If the message refers to delivery agent or courier details, e.g. 'who is delivering', 'delivery agent info', 'delivery boy details', 'courier name' → order.agent_info - If the message refers to notes or special instructions, e.g. 'add note', 'special instructions', 'add remark', 'leave a message for delivery' → order.add_note - If the message asks for general order details or information, e.g. 'order details', 'show order info', 'information about my order' → order.details - If the message asks about location, tracking, or where something is, even indirectly (e.g. 'track order', 'where is my shipment') → order.track_location - For anything else that doesn’t match above → unknown. Output Format: Return only the intent code (for example: order.track_location or order.cancel or unknown). Example Inputs & Outputs: - Message: 'Where is my order?' → order.track_location - Message: 'Cancel my purchase please' → order.cancel - Message: 'I want a refund' → order.request_refund - Message: 'Order delivered already' → order.update_delivered - Message: 'Is payment done?' → order.payment_status - Message: 'How did I pay?' → order.payment_method - Message: 'Who will deliver my parcel?' → order.agent_info - Message: 'Add special note for delivery' → order.add_note - Message: 'Tell me order info' → order.details - Message: 'When will my order arrive?' → order.estimate_delivery - Message: 'What is the total cost?' → order.details_amount - Message: 'Hello' → unknown"),
        ],
    ],

];
