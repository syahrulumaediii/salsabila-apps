<?php

// Pastikan folder storage di /tmp dibuat untuk serverless Vercel (read-only filesystem)
$storagePath = '/tmp/storage';
$directories = [
    $storagePath.'/app/public',
    $storagePath.'/framework/cache/data',
    $storagePath.'/framework/sessions',
    $storagePath.'/framework/views',
    $storagePath.'/logs',
];

foreach ($directories as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
}

putenv("APP_STORAGE={$storagePath}");
$_ENV['APP_STORAGE'] = $storagePath;
$_SERVER['APP_STORAGE'] = $storagePath;

// Otomatis deteksi APP_URL dari domain Vercel jika belum di-set manual
if (empty($_ENV['APP_URL']) && ! empty($_ENV['VERCEL_URL'])) {
    $vercelUrl = 'https://'.$_ENV['VERCEL_URL'];
    putenv("APP_URL={$vercelUrl}");
    $_ENV['APP_URL'] = $vercelUrl;
    $_SERVER['APP_URL'] = $vercelUrl;
}

// Teruskan request ke entry point Laravel di public/index.php
require __DIR__.'/../public/index.php';
