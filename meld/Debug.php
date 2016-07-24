<?php

class Debug {

  public static function var_dump($var, $label = '') {
    echo "<pre>{$label} ";
    print_r($var);
    echo '</pre>';
  }

  public static function var_dump_2($var, $label = '') {
    echo "<pre> {$label} ";
    print_r($var);
    echo '</pre>';
  }

}

?>
