<?php

class Config {
  protected $settings = [];

  public function __construct(array $config_files) {
    if (is_array($config_files) && 0 < count($config_files)) {
      foreach ($config_files as $name => $file_path) {
        $this->set($name, $file_path);
      }
    }
  }

  public function set($name, $file_path) {
    if (file_exists($file_path) && is_file($file_path)) {
      $this->settings[$name] = ( new JsonFile($file_path) )->json;
    }
  }

  public function get($name) {
    return $this->settings[$name] ?? null;
  }
}

?>
