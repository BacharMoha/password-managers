<?php
// Database configuration
define('DB_HOST', '127.0.0.1'); 
define('DB_USER', 'root');
define('DB_PASS', 'Salutation');
define('DB_NAME', 'password_manager');

// Application settings
define('APP_NAME', 'SecurePass Manager');
define('APP_VERSION', '1.0.0');
define('APP_SALT', 'your-unique-salt-here');

// Encryption settings
define('ENCRYPTION_METHOD', 'AES-256-CBC');
define('ENCRYPTION_KEY_LENGTH', 32);
define('ENCRYPTION_IV_LENGTH', openssl_cipher_iv_length(ENCRYPTION_METHOD));

// Session settings
session_start();

// Error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set timezone
date_default_timezone_set('UTC');

// Security checks
if (!function_exists('openssl_encrypt')) {
    die("OpenSSL n'est pas disponible sur ce serveur");
}

// Include required files

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Encryption.php';
require_once __DIR__ . '/functions.php';
?>