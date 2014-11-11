<?php

class ResponseResolver {

  protected $controller;

  protected $action;

  public function __construct( $controller, $action ) {

    $this->controller = $controller;
    $this->action = $action;
  
  }

  public function executeResponse( $vars ) {

    $content_type = '';

    if ( array_key_exists( 'Content-Type', getallheaders() ) ) {
      $content_type = getallheaders()['Content-Type'];
    }

    if ( $content_type == 'application/json' || $content_type == 'json' ) {

      require 'JsonResponse.php';

      $viewObject = new JsonResponse( $vars );

    } elseif ( $content_type == 'application/xml' || $content_type == 'xml' ) {

      require 'XmlResponse.php';

      $xmlName = $this->controller.'-'.$this->action;

      $viewObject = new XmlResponse( $xmlName, $vars );

    } else {

      require 'ViewResponse.php';

      $viewName = $this->controller.'/'.$this->action;

      $viewObject = new ViewResponse( $viewName, $vars );

    }

    $viewObject->execute();

  }

}

?>