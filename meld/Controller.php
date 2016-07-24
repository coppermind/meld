<?php

class Controller {
  public $before_action = [];
  public $after_action  = [];

  // TODO: implement skip callbacks in Dispatch
  public $skip_before_action = [];
  public $skip_after_action  = [];

  protected $params = [];
  protected $request = [];
  protected $loader  = '';

  protected $vars = [];
  protected $view = null;

  public function __construct(array $request = [], array $params = []) {
    $this->params = $params;
    $this->view = new View();
  }

}
?>
