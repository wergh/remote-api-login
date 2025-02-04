<?php

return [
    /*
     * |--------------------------------------------------------------------------
     * | Database table name
     * |--------------------------------------------------------------------------
     * | This field specifies the name of the database table that will be created
     * | to store the login requests received by the frontend. You can configure
     * | the name as you wish.
     */
    'table_name' => 'ral_request_data',

    /*
     * |--------------------------------------------------------------------------
     * | Login request response
     * |--------------------------------------------------------------------------
     * | The login request sends 3 parameters as a response: a UUID, a token, and
     * | a code. These are the 3 elements necessary for the correct functioning
     * | of the library. You can configure the length and lifetime of these elements.
     * |
     * | The code is an uppercase string with only letters (the letter 'O' is
     * | avoided to prevent confusion with zero). It must be long enough to be
     * | unique during its lifetime but simple enough since, depending on your
     * | frontend, the user may have to type it. The library will verify that the
     * | code is unique before returning it, generating one and checking if it is
     * | currently in use. By default, the generated code is 8 characters long
     * | with an expiration time of 300 seconds. Since 24 characters are used to
     * | generate an 8-letter code (which can be repeated), we are talking about
     * | 24^8 possibilities, i.e., more than 110 billion possibilities that
     * | regenerate every 5 minutes. If you think this is not enough, you can
     * | increase the values as much as you want. The same applies to the token.
     * | If you think a random token of 32 characters (including uppercase,
     * | lowercase, numbers, and letters) is not secure enough, feel free to
     * | modify it as you wish.
     */
    'code_length' => 8,
    'expiration_time_in_seconds' => 300,
    'token_length' => 32,

    /*
     * |--------------------------------------------------------------------------
     * | Request and token URLs
     * |--------------------------------------------------------------------------
     * | These fields specify the URLs used for the login request and token
     * | retrieval. You can configure them according to your application's routes.
     */
    'request_url' => env('APP_URL').'/api/login-request',
    'token_url' => env('APP_URL').'/api/get-token',

    /*
     * |--------------------------------------------------------------------------
     * | WebSocket channel parameters
     * |--------------------------------------------------------------------------
     * | Here we can configure both the name of the channel (to which we will
     * | later concatenate the UUID of the user making the request) and the event
     * | that is sent. For example, if the channel_socket_name is 'remote-login'
     * | and the user's UUID is '1111-11111-11111-11111-11111', the broadcast
     * | channel used will be:
     * | 'remote-login.1111-11111-11111-11111-11111'
     */
    'channel_socket_name' => 'remote-login',
    'broadcast_event' => 'LoginSuccessfully',

    /*
     * |--------------------------------------------------------------------------
     * | API authentication driver
     * |--------------------------------------------------------------------------
     * | The package can work with the two standard Laravel libraries (Passport or
     * | Sanctum) or, if you use another library or even a custom method, you can
     * | choose the 'custom' system. If you choose 'custom', you must uncomment
     * | the 'class' and 'method' lines and specify the class and method that the
     * | library should call to generate the token.
     */
    'auth_driver' => 'passport', // Options: 'sanctum', 'passport', 'custom'
    'custom' => [
        //    'class' => App\Services\CustomTokenService::class, // Custom class where the token is generated
        //    'method' => 'createToken', // Custom method called to generate the token
    ],

    /*
     * |--------------------------------------------------------------------------
     * | Return parameters
     * |--------------------------------------------------------------------------
     * | It is necessary to specify the names of the return parameters that the
     * | token will return once generated. In the array, you can see the possible
     * | values to return and how they will be named in the response.
     * |
     * | In the case of Passport, it can return all 3 values (access_token,
     * | refresh_token, and expires_in). In the case of Sanctum, only the
     * | access_token will be returned. If you need additional parameters, feel
     * | free to use the custom option. With this option, you will retrieve
     * | everything that you return to the library. You can choose the custom
     * | option with Passport, Sanctum, or your own method, customize and
     * | configure everything, and then return the array result to the library.
     * | It will return exactly what you provide to it.
     */
    'returned_params' => [
        'access_token' => 'access_token',
        'refresh_token' => 'refresh_token',
        'expires_in' => 'expires_in'
    ],

    /*
     * |--------------------------------------------------------------------------
     * | Token expiration time
     * |--------------------------------------------------------------------------
     * | The expiration time of the tokens in days. You can use an environment
     * | variable or specify the value directly here.
     */
    'access_token_expiration_time' => env('ACCESS_TOKEN_EXPIRATION_TIME', 15),
    'refresh_token_expiration_time' => env('REFRESH_TOKEN_EXPIRATION_TIME', 30),
];
