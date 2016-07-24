<?php

class Inflector {

  public static function pluralize($word) {
    //
  }

  public static function singularize($word) {
    //
  }

  public static function camelize($word) {
    $words = [];
    $return = $word;

    $word = str_replace('_', ' ', $word);
    $word = ucwords($word);
    $word = str_replace(' ', '', $word);
    return $word;
  }

  public static function underscore($word) {
    //
  }

  public static function humanize($word) {
    //
  }

  public static function capitalize($word) {
    //
  }

  public static function ucase_first($word) {
    //
  }

  public static function titleize($word) {
    //
  }

  public static function tableize($word) {
    //
  }

  public static function classify($word) {
    //
  }

  public static function dasherize($word) {
    //
  }

  public static function constantize($word) {
    //
  }

  public static function ordinal($word) {
    //
  }

  public static function ordinalize($word) {
    //
  }
}

?>
