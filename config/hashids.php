<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HashIds Salt
    |--------------------------------------------------------------------------
    |
    | This value is used as the salt for generating HashIds. It should be
    | a long, random string to ensure secure hash generation.
    |
    */
    'salt' => env('HASHIDS_SALT', env('APP_KEY')),

    /*
    |--------------------------------------------------------------------------
    | HashIds Minimum Length
    |--------------------------------------------------------------------------
    |
    | This value determines the minimum length of generated HashIds.
    | A longer length provides more security but longer URLs.
    |
    */
    'min_length' => 10,

    /*
    |--------------------------------------------------------------------------
    | HashIds Alphabet
    |--------------------------------------------------------------------------
    |
    | This value determines the characters used in generating HashIds.
    | The default includes lowercase and uppercase letters and numbers.
    |
    */
    'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890',
];
