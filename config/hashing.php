<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default hash driver that is used to verify
    | user passwords. A variety of drivers are provided, including bcrypt
    | and argon2, which are supported by standard PHP.
    |
    | Supported: "bcrypt", "argon", "argon2id"
    |
    */

    'driver' => 'argon2id',

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options that should be used when
    | the passwords are hashed using the Bcrypt algorithm. This allows you
    | to control the amount of time it takes to hash each password.
    |
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon Options
    |--------------------------------------------------------------------------
    |
    | Here you may specify the configuration options that should be used when
    | the passwords are hashed using the Argon algorithm. These will allow
    | you to control the amount of time it takes to hash each password.
    |
    */

    'argon' => [
        'memory' => 65536,
        'threads' => 1,
        'time' => 4,
    ],

];
