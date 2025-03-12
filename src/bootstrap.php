<?php

// Make sure we have the vendor autoloader
$autoloadPaths = [
    __DIR__ . '/../vendor/autoload.php',  // Normal path
    __DIR__ . '/vendor/autoload.php',     // Alternative path
];

$loaded = false;
foreach ($autoloadPaths as $path) {
    if (file_exists($path)) {
        require $path;
        $loaded = true;
        break;
    }
}

if (!$loaded) {
    die('Vendor autoload file not found. Please run "composer install"');
}

// Set environment variables
$dotenv = __DIR__ . '/../.env';
if (file_exists($dotenv)) {
    $lines = file($dotenv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Set default timezone
date_default_timezone_set('UTC');

// Make database directory if it doesn't exist
$databaseDir = __DIR__ . '/../database';
if (!file_exists($databaseDir)) {
    mkdir($databaseDir, 0755, true);

    // Create an empty SQLite file
    $dbFile = $databaseDir . '/database.sqlite';
    if (!file_exists($dbFile)) {
        file_put_contents($dbFile, '');
        chmod($dbFile, 0644);
    }
}

if (!file_exists(__DIR__ . '/../cache')) {
    mkdir(__DIR__ . '/../cache', 0755, true);
}