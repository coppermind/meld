<?php

class ConfigJson {

  public $config = [];

  public function __construct($file_path) {
    $this->config = ( new JsonFile($file_path) )->json;
    return $this;
  }

}

?>
