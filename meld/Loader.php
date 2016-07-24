<?php

class Loader {

  public static $NAMESPACE = '';

   /** Autoloaders */
  public static function register() {
    spl_autoload_register('Loader::autoload');
  }

  public static function autoload($class_name) {
    if (strpos($class_name, 'Controller') !== false) {
      self::autoloadController($class_name);
      return true;
    }
    if (strpos($class_name, 'Mailer') !== false) {
      self::autoloadMailer($class_name);
      return true;
    }
    if (strpos($class_name, 'Helper') !== false) {
      self::autoloadHelper($class_name);
      return true;
    }
    if (self::autoloadModel($class_name)) {
      return true;
    }
    if (self::autoloadLibrary($class_name)) {
      return true;
    }
  }

  public static function autoloadController($controller_name) {
    $namespaced_path = APP_DIR . '/controllers/'. Loader::$NAMESPACE . '/' . $controller_name . '.php';
    $regular_path = APP_DIR . '/controllers/'. $controller_name . '.php';
    if (self::loadFile($namespaced_path) || self::loadFile($regular_path)) {
      return true;
    }
  }

  public static function autoloadMailer($mailer_name) {
    $namespaced_path = APP_DIR . '/mailers/'. Loader::$NAMESPACE . '/' . $mailer_name . '.php';
    $regular_path = APP_DIR . '/mailers/'. $mailer_name . '.php';
    if (self::loadFile($namespaced_path) || self::loadFile($regular_path)) {
      return true;
    }
  }

  public static function autoloadHelper($helper_name) {
    $namespaced_path = APP_DIR . '/helpers/'. Loader::$NAMESPACE . '/' . $helper_name . '.php';
    $regular_path = APP_DIR . '/helpers/'. $helper_name . '.php';
    if (self::loadFile($namespaced_path) || self::loadFile($regular_path)) {
      return true;
    }
  }

  public static function autoloadModel($model_name) {
    $namespaced_path = APP_DIR . '/models/'. Loader::$NAMESPACE . '/' . $model_name . '.php';
    $regular_path = APP_DIR . '/models/'. $model_name . '.php';
    if (self::loadFile($namespaced_path) || self::loadFile($regular_path)) {
      return true;
    }
  }

  public static function autoloadLibrary($library_name) {
    $namespaced_path = APP_DIR . '/libraries/'. Loader::$NAMESPACE . '/' . $library_name . '.php';
    $regular_path = APP_DIR . '/libraries/'. $library_name . '.php';
    if (self::loadFile($namespaced_path) || self::loadFile($regular_path)) {
      return true;
    }
  }
  /** End: Autoloaders */


  public static function setNamespace(array $namespace = []) {
    self::$NAMESPACE = implode('/', $namespace);
  }

  public static function loadFile($file_path) {
    if (file_exists($file_path) && is_file($file_path)) {
      require($file_path);
      return true;
    }
  }

  public static function useMailer($mailer_name, $namespace = '') {
    $full_path = APP_DIR . '/mailers' . (empty($namespace) ? '/' : $namespace) . $mailer_name . '.php';
    if (self::loadFile($full_path)) {
      return true;
    }
  }

  public static function useHelper($helper_name, $namespace = '') {
    $full_path = APP_DIR . '/mailers' . (empty($namespace) ? '/' : $namespace) . $helper_name . '.php';
    if (self::loadFile($full_path)) {
      return true;
    }
  }

  public static function useLibrary($library_name, $namespace = '') {
    $full_path = APP_DIR . '/mailers' . (empty($namespace) ? '/' : $namespace) . $library_name . '.php';
    if (self::loadFile($full_path)) {
      return true;
    }
  }
}
