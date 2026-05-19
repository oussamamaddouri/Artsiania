<?php
// Use Vercel Env Vars
define('DB_HOST', getenv('DB_HOST'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASS', getenv('DB_PASS'));
define('DB_NAME', getenv('DB_NAME'));

define('SITE_NAME', 'Artisania');
// Vercel provides a system URL automatically
define('BASE_URL', 'https://' . getenv('VERCEL_URL'));