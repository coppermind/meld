<?php

class UsersController extends Controller {
  public $before_action = [
    'debug',
    'test' => ['except' => ['index']],
  ];


  public function __construct(array $request = [], array $params = []) {
    parent::__construct($request, $params);
  }

  public function indexAction() {
  }

  public function debug() {
    echo 'before_action: debug()<br>';
  }

  public function test() {
    echo 'before_action: test()<br>';
  }
}
?>
