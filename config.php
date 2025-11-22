<?php
// config.php
// Put this outside webroot if possible, or protect via server config

// Database config — change to your values
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'communityfix');
define('DB_USER', 'root');
define('DB_PASS', '');  // leave EMPTY
define('DB_CHARSET', 'utf8mb4');



// Uploads
define('UPLOAD_DIR', __DIR__ . '/uploads'); // make sure this folder exists and is writable
define('UPLOAD_MAX_SIZE', 10 * 1024 * 1024); // 10 MB
$allowed_mime = ['image/jpeg','image/png','image/webp','image/gif'];

function getPDO() {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $opts = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    return new PDO($dsn, DB_USER, DB_PASS, $opts);
}
