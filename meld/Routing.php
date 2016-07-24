<?php

class Routing {
  protected $config = [];

  protected $request = [
    'namespace'   => [],
    'controller'  => '',
    'action'      => ''
  ];

  protected $method = '';

  protected $controller_config = [];

  protected $default_actions = [
    'resources' => [
      'GET'   => ['index','show','new','edit'],
      'POST'  => ['create','update','delete']
    ],
    'resource' => [
      'GET'   => ['show','new','edit'],
      'POST'  => ['create','update','delete']
    ]
  ];


  public function __construct(Config $config) {
    $this->method = $_SERVER['REQUEST_METHOD'];
    $this->loadJsonConfig($config);
    $this->resolveRequest();
  }

  public function config() {
    return $this->config ?? [];
  }

  public function request() {
    return $this->request;
  }

  public function method() {
    return $this->method ?? null;
  }

  public function controllerConfig() {
    return $this->controller_config ?? [];
  }

  protected function loadJsonConfig($config) {
    $this->config = $config->get('routes');
    if (is_null($this->config) || empty($this->config) || count($this->config) == 0) {
      die('Routes file empty or misconfigured');
    }
  }

  // TODO: possible refactoring candidate
  protected function resolveRequest() {
    $params = $_REQUEST['_route'] ?? ''; // $_REQUEST contains $_GET, $_POST, and $_COOKIE variables
    $params = explode('/', $params);
    array_shift($params); // remove the first element which contains an empty string
    if ( empty($params[count($params)-1]) ) {
      array_pop($params);
    }

    if (count($params) > 0) {
      $params_count = count($params);
      if ($params_count > 1) {
        $this->parseParams($params, $params_count);

      // route param only contains controller
      } else {
        $controller = $params[0];
        if (array_key_exists($controller, $this->config)) {
          $this->request['controller'] = $controller;
          $this->controller_config = $this->config[$controller];
          $this->assignDefaultAction();
        } else {
          throw new RouteException('No route matches', RouteException::NO_ROUTE_MATCH);
        }
      }

    // no route info in the request
    } else {
      $this->getRoot();
    }

    $this->isActionAllowed();
  }

  protected function parseParams($params, $params_count) {
    $previous = [];
    for ($i = 1; $i <= $params_count; $i++) {
      $item = $params[$i-1];
      switch ($i) {
        case 1:
          $current = $this->config[$item] ?? null;
          break;
        case 2:
          $namespace_1 = $this->request['namespace'][0];
          $current = $this->config[$namespace_1][$item] ?? null;
          break;
        case 3:
          $namespace_1 = $this->request['namespace'][0];
          $namespace_2 = $this->request['namespace'][1];
          $current = $this->config[$namespace_1][$namespace_2][$item] ?? null;
          break;
        case 4:
          $namespace_1 = $this->request['namespace'][0];
          $namespace_2 = $this->request['namespace'][1];
          $namespace_3 = $this->request['namespace'][2];
          $current = $this->config[$namespace_1][$namespace_2][$namespace_3][$item] ?? null;
          break;
      }

      #-- /namespace/controller/action

      if (is_null($current) && count($this->request['namespace']) == 0) {
        throw new RouteException('No route matches', RouteException::NO_ROUTE_MATCH);

      } elseif (is_null($current) && count($this->request['namespace']) > 0) {
        if ($this->request['controller'] == $this->request['namespace'][count($this->request['namespace'])-1]) {
          array_pop($this->request['namespace']); // last defined namespace is the controller
          $this->request['action'] = $item;
          break;
        }

      } else {
        if ($params_count > ($i - 1)) {
          $this->request['namespace'][] = $item;
        }
        $this->request['controller'] = $item;
      }

      $previous = $current;

    } // end: for ($i = 1; $i <= $params_count; $i++)

    if (is_null($current)) {
      $this->controller_config = $previous;
    } else {
      $this->controller_config = $current;
    }

    if ($this->request['controller'] == $this->request['namespace'][count($this->request['namespace'])-1]) {
      array_pop($this->request['namespace']); // last defined namespace is the controller
    }

    if (empty($this->request['action'])) {
      $this->assignDefaultAction();
    }
  }

  protected function getRoot() {
    $root = $this->config['root'] ?? '';
    if (gettype($root) == 'string' && strpos($root, '#') !== false) {
      list($this->request['controller'], $this->request['action']) = explode('#', $root);

    } elseif (gettype($root) == 'array') {
      $this->request['controller']  = $root['controller'];
      $this->request['action']      = $root['action'];
    }

    if (empty($this->request['controller'])) {
      throw new RouteException('No root path defined', RouteException::ROOT_UNDEFINED);
    }
  }

  protected function assignDefaultAction() {
    if ($this->controller_config['type'] == 'resource') {
      $this->request['action'] = 'show';
    } else  {
      $this->request['action'] = 'index';
    }
  }

  /**
   * Heirarchy:
   * Only > Except > Collection|Member > Defaults
   */
  protected function isActionAllowed() {
    $action = $this->request['action'];

    // Only
    if (isset($this->controller_config['only']) && 0 < count($this->controller_config['only']) ) {
      if (!in_array($this->request['action'], $this->controller_config['only'])) {
        throw new RouteException('Route not allowed', RouteException::ACTION_INVALID);
      }
    }

    // Except
    if (isset($this->controller_config['except']) && 0 < count($this->controller_config['except']) ) {
      if (in_array($this->request['action'], $this->controller_config['except'])) {
        throw new RouteException('Route not allowed', RouteException::ACTION_INVALID);
      }
    }

    // Collection
    if (isset($this->controller_config['collection']) && 0 < count($this->controller_config['collection']) ) {
      if (in_array($this->request['action'], $this->controller_config['collection'])) {
        return true;
      }
    }

    // Member
    if (isset($this->controller_config['member']) && 0 < count($this->controller_config['member']) ) {
      if (in_array($this->request['action'], $this->controller_config['member'])) {
        return true;
      }
    }

    // Defaults
    if (isset($this->controller_config['type']) && 'resource' == $this->controller_config['type']) {
      $defaults = $this->default_actions['resource'];
    } else {
      $defaults = $this->default_actions['resources'];
    }

    if (!in_array($this->request['action'], $defaults[$this->method])) {
      throw new RouteException('Route not allowed', RouteException::ACTION_INVALID);
    }

  }

}

?>
