<?php

class UsersController extends Controller {

  public function __construct(array $request = [], array $params = []) {
    parent::__construct($request, $params);
    echo 'UsersController 2!<br>';
  }
}
?>
