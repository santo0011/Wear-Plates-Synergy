<?php

/*
| Default admin account used by Database\Seeders\AdminSeeder.
| Values come from .env so no credentials are committed to source code.
*/
return [
    'name' => env('ADMIN_NAME', 'Site Administrator'),
    'email' => env('ADMIN_EMAIL'),
    'password' => env('ADMIN_PASSWORD'),
];
