<?php

class Token {

  public static function get() {
    return Session::get('csrf_token') ?? self::set();
  }

  public static function set() {
    $token = bin2hex(random_bytes(64));
    Session::set('csrf_token', $token);
  }

  public static function validate($token) {
    return hash_equals($token, Session::get('csrf_token'));
  }

}

?>
