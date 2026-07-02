<?php
/**
 * YTech Panels — Bootstrap Configuration
 * Loads .env via vlucas/phpdotenv (already in composer.json)
 * Include this file FIRST in any PHP page that needs env variables.
 */

// Autoload vendor packages
require_once dirname(__DIR__) . '/vendor/autoload.php';

use Dotenv\Dotenv;

// Load .env from the project root
try {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->load();

    // Required variables — will throw an exception if any are missing
    $dotenv->required([
        'DB_HOST',
        'DB_PORT',
        'DB_NAME',
        'DB_USER',
        'APP_ENV',
    ])->notEmpty();
} catch (Throwable $e) {
    error_log('Environment bootstrap warning: ' . $e->getMessage());
}
