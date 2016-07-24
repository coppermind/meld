<?php
error_reporting(E_ALL);

// TODO: replace `dirname` with `realpath`
// Get parent directory of current working directory `public`
define('APP_PATH', realpath('..'));
define('APP_DIR',  APP_PATH . '/app');

require_once('../config/bootstrap.php');
?>
