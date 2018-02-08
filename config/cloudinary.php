<?php
$name = getenv('CLOUD_NAME');
$key = getenv('CLOUD_API_KEY');
$secret = getenv('CLOUD_API_SECRET');
return [
    'cloudinary' => [
        'cloud_name' => env('CLOUD_NAME', $name),
        'api_key' => env('CLOUD_API_KEY', $key),
        'api_secret' => env('CLOUD_API_SECRET', $secret)
    ]
];
