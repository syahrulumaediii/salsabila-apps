<?php

// Pastikan folder storage di /tmp dibuat untuk serverless Vercel (read-only filesystem)
$storagePath = '/tmp/storage';
$bootstrapCache = '/tmp/bootstrap/cache';

$directories = [
    $storagePath.'/app/public',
    $storagePath.'/framework/cache/data',
    $storagePath.'/framework/sessions',
    $storagePath.'/framework/views',
    $storagePath.'/logs',
    $bootstrapCache,
];

foreach ($directories as $directory) {
    if (! is_dir($directory)) {
        mkdir($directory, 0755, true);
    }
}

// Redirect storage and bootstrap cache to writable /tmp
putenv("APP_STORAGE={$storagePath}");
putenv("APP_PACKAGES_CACHE={$bootstrapCache}/packages.php");
putenv("APP_SERVICES_CACHE={$bootstrapCache}/services.php");
putenv("APP_CONFIG_CACHE={$bootstrapCache}/config.php");
putenv("APP_ROUTES_CACHE={$bootstrapCache}/routes-v7.php");
putenv("APP_EVENTS_CACHE={$bootstrapCache}/events.php");
putenv("VIEW_COMPILED_PATH={$storagePath}/framework/views");

$_ENV['APP_STORAGE'] = $storagePath;
$_ENV['APP_PACKAGES_CACHE'] = "{$bootstrapCache}/packages.php";
$_ENV['APP_SERVICES_CACHE'] = "{$bootstrapCache}/services.php";
$_ENV['APP_CONFIG_CACHE'] = "{$bootstrapCache}/config.php";
$_ENV['APP_ROUTES_CACHE'] = "{$bootstrapCache}/routes-v7.php";
$_ENV['APP_EVENTS_CACHE'] = "{$bootstrapCache}/events.php";
$_ENV['VIEW_COMPILED_PATH'] = "{$storagePath}/framework/views";

$_SERVER['APP_STORAGE'] = $storagePath;
$_SERVER['APP_PACKAGES_CACHE'] = "{$bootstrapCache}/packages.php";
$_SERVER['APP_SERVICES_CACHE'] = "{$bootstrapCache}/services.php";
$_SERVER['APP_CONFIG_CACHE'] = "{$bootstrapCache}/config.php";
$_SERVER['APP_ROUTES_CACHE'] = "{$bootstrapCache}/routes-v7.php";
$_SERVER['APP_EVENTS_CACHE'] = "{$bootstrapCache}/events.php";
$_SERVER['VIEW_COMPILED_PATH'] = "{$storagePath}/framework/views";

// Otomatis deteksi APP_URL dari domain Vercel jika belum di-set manual
if (empty($_ENV['APP_URL']) && ! empty($_ENV['VERCEL_URL'])) {
    $vercelUrl = 'https://'.$_ENV['VERCEL_URL'];
    putenv("APP_URL={$vercelUrl}");
    $_ENV['APP_URL'] = $vercelUrl;
    $_SERVER['APP_URL'] = $vercelUrl;
}

// Teruskan request ke entry point Laravel di public/index.php
require __DIR__.'/../public/index.php';
