<?php

/**
 * @var Laravel\Lumen\Routing\Router $router
 */
$router->group([
    'prefix' => env('APP_API_VERSION', ''),
    'namespace' => '\App\Api\V1\Controllers'
], function ($router) {
    /**
     * PUBLIC ACCESS
     */
//    $router->group([], function ($router) {
//    });

    /**
     * USER APPLICATION PRIVATE ACCESS
     */
    $router->group([
        'prefix' => 'auth',
        'as' => 'auth',
        "namespace" => "OneStepId",
    ], function ($router) {
        $router->post('/send-phone/{botID}', "UserRequestsRegistrationByPhoneNumber");
        $router->post('/send-sms/{botID}', "SendTokenSmsToUser");
        $router->post('/send-username', "UserSubmitsUsername");
        $router->post('/send-code', "VerifyPhoneNumber");

        $router->post('/refresh-token', 'AuthController@refresh');
    });

    /**
     * PUBLIC ACCESS - CREATE USER ACCOUNT
    */
    $router->group([
        'prefix' => 'user-account',
        "namespace" => "OneStepId2"
    ], function ($router) {
        $router->post('/create', "CreateUserIDController@createAccount");
        $router->post('/otp/resend', "CreateUserIDController@resendOTP");
        $router->post('/otp/verify', "CreateUserIDController@verifyOTP");
        $router->post('/update', "CreateUserIDController@updateUser");
        $router->post('/update/recovery', "CreateUserIDController@updateRecoveryQuestion");
    });

    /**
     * PRIVATE ACCESS
    */
    $router->group(['middleware' => 'checkUser'], function ($router) {
        /**
         * CREATE USER ACCOUNT
        */
        $router->group([
            'prefix' => 'user-profile',
            "namespace" => "OneStepId2"
        ], function ($router) {
            $router->get('/{id}/details', "UserProfileController@getProfile");
            $router->put('/password/change', "UserProfileController@updatePassword");
            $router->put('/username/update', "UserProfileController@updateUsername");
            $router->put('/fullname/update', "UserProfileController@updateFullname");
            $router->put('/country/update', "UserProfileController@updateCountry");
            $router->put('/email/update', "UserProfileController@updateEmail");
            $router->put('/local/update', "UserProfileController@updateLocal");
        });

        /**
         * USER SOCIAL MEDIA CONNECTIONS
        */
        $router->group([
            'prefix' => 'user-profile',
            "namespace" => "OneStepId2"
        ], function ($router) {
            $router->post('/redirect', "SocialMediaController@createRedirectUrl");
            $router->get('/{provider}/callback', "SocialMediaController@mediaCallback");
            $router->get('/social/connections', "SocialMediaController@getMediaData");
        });
    });

<<<<<<< HEAD
    
    
=======
>>>>>>> 4cf86396f4c510e3e65bb8bf4d0692e5bb7a7991
    /**
     * PUBLIC ACCESS - RECOVER USER ACCOUNT
    */
    $router->group([
        'prefix' => 'user-account/recovery',
        "namespace" => "OneStepId2"
    ], function ($router) {
        $router->post('/userinfo', "UserInfoRecoveryController@recoveryInfo");
        $router->post('/otp/verify', "UserInfoRecoveryController@verifyOTP");
        $router->post('/questions', "UserInfoRecoveryController@recoveryQuestions");
        $router->post('/sendid', "UserInfoRecoveryController@sendRecoveredID");
    });

    /**
     * PRIVATE ACCESS
<<<<<<< HEAD
    */
=======
     */
>>>>>>> 4cf86396f4c510e3e65bb8bf4d0692e5bb7a7991
    $router->group(['middleware' => 'auth:api'], function ($router) {
        $router->get('users', 'UserController@index');
    });

    $router->group(['middleware' => 'checkUser'], function ($router) {
        $router->group(['prefix' => 'users', 'as' => 'users'], function ($router) {
            $router->post('/', 'UserController@store');
            $router->get('/{id}', 'UserController@show');
            $router->patch('/{id}', 'UserController@update');
            $router->post('/validate-edit-phone', 'UserController@validateEditPhoneNumber');
            $router->post('/update-phone', 'UserController@updateMyPhoneNumber');
            $router->post('/identify', 'UserController@identifyStart');
            $router->post('/identify-webhook', 'UserController@identifyWebHook');
        });
    });

    /**
     * ADMIN PANEL ACCESS
     */
    $router->group([
        'prefix' => 'admin',
        'namespace' => 'Admin',
        'middleware' => [
            'checkUser',
            'checkAdmin'
        ]
    ], function ($router) {
        $router->group([
            'prefix' => 'users',
            'as' => 'admin.users',
        ], function () use ($router) {
            $router->get('/', 'UserController@index');
            $router->post('/', 'UserController@store');
            $router->get('/{id}', 'UserController@show');
            $router->patch('/{id}', 'UserController@approve');
            $router->delete('/{id}', 'UserController@destroy');
            $router->post('/verify', 'UserController@verify');
            $router->post('/verify/send', 'UserController@verify_email');
        });

        /**
         * Add Admins to microservice
         */
        $router->post('/service/admins', 'ServiceAdminController@store');
        $router->patch('/service/admins', 'ServiceAdminController@update');
        $router->delete('/service/admins', 'ServiceAdminController@destroy');
    });
});
