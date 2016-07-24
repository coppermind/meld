<?php
if (!defined('APP_PATH')) die('Cannot call this file directly');

require_once(APP_PATH . '/meld/dispatch.php');

$config = new Config([
  'app'       => APP_PATH . '/config/app.json',
  'database'  => APP_PATH . '/config/database.json',
  'routes'    => APP_PATH . '/config/routes.json',
]);

$app = new Dispatch($config);
?>
