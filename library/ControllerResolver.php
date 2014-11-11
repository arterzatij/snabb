<?php

class ControllerResolver {

  protected $controller;

  protected $action;

  public function __construct( $controller, $action ) {

    $this->controller = $controller;
    $this->action = $action;
  
  }

  public function executeController( $params ) {

    $controllerFileName  = $this->getControllerFileName();
    $controllerClassName = $this->getControllerClassName();
    $actionMethodName    = $this->getActionMethodName();
    
    if ( file_exists( $controllerFileName ) ) {

      // ... the controller exists
      require $controllerFileName;

      $controller = new $controllerClassName();

      if ( method_exists( $controller, $actionMethodName ) ) {

        return call_user_func_array( [ $controller, $actionMethodName ], $params );

      } else {

        throw new Exception("Action on controller " . $this->controller . " :: " . $this->action . " not found", 404);
        
      }

    } else {

      throw new Exception("No such controller: " . $this->controller, 404);
      
    }

  }

  protected function getControllerClassName() {

    return Inflector::camel( $this->controller ) . 'Controller';

  }

  protected function getControllerFileName() {

    return 'app/controllers/' . $this->getControllerClassName() . '.php';

  }

  protected function getActionMethodName() {

    return Inflector::lowerCamel( $this->action ) . 'Action';

  }

}

?>