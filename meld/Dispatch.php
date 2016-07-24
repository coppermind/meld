<?php
if (!defined('APP_PATH')) die('Cannot call this file directly');


require_once(APP_PATH . '/meld/exceptions/_exceptions.php');
require_once(APP_PATH . '/meld/Config.php');
require_once(APP_PATH . '/meld/ConfigJson.php');
require_once(APP_PATH . '/meld/Controller.php');
require_once(APP_PATH . '/meld/Debug.php');
require_once(APP_PATH . '/meld/Inflector.php');
require_once(APP_PATH . '/meld/JsonFile.php');
require_once(APP_PATH . '/meld/Loader.php');
require_once(APP_PATH . '/meld/Routing.php');
require_once(APP_PATH . '/meld/Token.php');
require_once(APP_PATH . '/meld/View.php');


class Dispatch {
  private $params = [];
  private $routing = null;

  public function __construct(Config $config) {
    try {
      $this->routing = new Routing($config);
      $this->initLoader();
      $this->getParams();
      $this->loadController();
    } catch (RouteException $e) {
      $this->show_exception('Routing Error', $e);
    } catch (Exception $e) {
      $this->show_exception('Error', $e);
    }
  }

  private function initLoader() {
    Loader::setNamespace($this->routing->request()['namespace']);
    Loader::register();
  }

  private function getParams() {
    if ($this->routing->method() == 'GET') {
      $this->params = $_GET;
    } elseif ($this->routing->method() == 'POST') {
      $this->params = $_POST;
      $this->checkCsrfToken(); // csrf token is required for post requests
    } else {
      throw new Exception('Unsupported request method');
    }
    unset($this->params['_route']); // routing info in params not needed beyond dispatch level
    // TODO: set controller and action in params?
  }

  private function checkCsrfToken() {
    if (!Token::validate($this->params['csrf_token'])) {
      throw new Exception('Invalid CSRF Token');
    }
  }

  private function loadController() {
    // build controller path
    $controller_class = Inflector::camelize($this->routing->request()['controller']) . 'Controller';
    $controller = new $controller_class($this->routing->request(), $this->params);
    $this->callAction($controller);
  }

  private function callAction($controller) {
    $action_name = $this->routing->request()['action'] . 'Action';
    if (method_exists($controller, $action_name)) {
      $this->runBeforeActions($controller);
      $controller->$action_name();
      $this->runAfterActions($controller);
    } else {
      throw new Exception($this->routing->request()['action'] . ' action is missing from ' . explode(' ', print_r($controller, 1))[0]);
    }
  }

  private function runBeforeActions($controller) {
    if (0 < count($controller->before_action)) {
      $this->runCallback($controller, $controller->before_action);
    }
  }

  private function runAfterActions($controller) {
    if (0 < count($controller->after_action)) {
      $this->runCallback($controller, $controller->after_action);
    }
  }

  private function runCallback($controller, $config) {
    foreach ($config as $method => $value) {
      if (is_array($value)) {
        $only   = $value['only'] ?? null;
        $except = $value['except'] ?? null;
        $run    = false;
        if (!is_null($only) && 0 < count($only)) {
          if (in_array($this->routing->request()['action'], $only)) {
            $run = true;
          }
        } elseif (!is_null($except)) {
          if (!in_array($this->routing->request()['action'], $except)) {
            $run = true;
          }
        } else {
          $run = true;
        }
        if ($run) {
          $controller->$method();
        }
      } else {
        $controller->$value();
      }
    }
  }

  private function show_exception($title, $e) {
  echo <<<EOT
<div style="color:#ff0000">
  <h3 style="margin:0px;">{$title} ({$e->getCode()})</h3>
  <hr>
  {$e->getMessage()}
</div>
EOT;
}

}

?>
