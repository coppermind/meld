<?php

class RouteException extends Exception {

  const NO_ROUTE_MATCH = 1;
  const ROOT_UNDEFINED = 2;
  const ACTION_INVALID = 3;

  public function __construct($message, $code, Exception $previous = null) {
    parent::__construct($message, $code, $previous);
  }

}

?>
