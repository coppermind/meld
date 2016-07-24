<?php

class Session {

  public static function start() {
    if (session_id() === '') {
      session_start();
    }
  }

  public static function get($name) {
    self::start();
    return $_SESSION[$name] ?? null;
  }

  public static function set($name, $value) {
    self::start();
    $_SESSION[$name] = $value;
  }

}

?>
