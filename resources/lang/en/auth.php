<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed' => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    'login' => [
        'title' => 'Login',
        'remember_me' => 'Remember me',
        'or' => 'OR',
        'submit' => 'Login',
        'login_with_facebook' => 'Login with Facebook',
        'password_forgotten' => 'Forgot your password?',
        'new_account' => 'Register a new account',
        'fields' => [
            'email' => 'Email',
            'password' => 'Password',
        ]
    ],
    'register' => [
        'title' => 'Register',
        'fields' => [
            'email' => 'Email',
            'name' => 'Username',
            'password' => 'Password',
            'password_confirmation' => 'Password confirmation',
        ],
        'submit' => 'Register',
        'register_with_facebook' => 'Register with Facebook',
        'already_account' => 'Already have an account?',
    ],
    'reset' => [
        'title' => 'Reset password',
        'submit' => 'Reset password',
        'send' => 'Send Password Reset Link',
        'fields' => [
            'email' => 'Email Address',
            'password' => 'Password',
            'password_confirmation' => 'Confirm your password',
        ]
    ]
];