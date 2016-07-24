<?php

class JsonFile {
  public $json = '';
  public $contents = '';

  public function __construct($file_path) {
    if (file_exists($file_path)) {
      $this->contents = file_get_contents($file_path);
      $this->json = json_decode($this->contents, true);
      return $this;
    }
  }
}

?>
