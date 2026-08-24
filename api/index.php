<?php

// Tentukan direktori sementara yang diizinkan untuk menulis file di Vercel
$tmpStorage = '/tmp/storage';
$tmpBootstrap = '/tmp/bootstrap/cache';

// Buat foldernya jika belum ada
foreach ([$tmpStorage, $tmpStorage.'/framework/views', $tmpStorage.'/framework/cache', $tmpStorage.'/framework/sessions', $tmpBootstrap] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Alihkan path default Laravel ke direktori /tmp
$_ENV['APP_SERVICES_CACHE'] = $tmpBootstrap . '/services.php';
$_ENV['APP_PACKAGES_CACHE'] = $tmpBootstrap . '/packages.php';
$_ENV['APP_CONFIG_CACHE'] = $tmpBootstrap . '/config.php';
$_ENV['VIEW_COMPILED_PATH'] = $tmpStorage . '/framework/views';

// Muat index Laravel yang asli
require __DIR__ . '/../public/index.php';
