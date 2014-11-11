<?php

class JsonResponse extends Response {

  protected $vars = array();

  public function __construct( $vars = array() ) {

    $this->vars = $vars;

  }

  public function execute() {

  	header("Content-type:application/json");

    echo json_encode( $this->vars );

  }

}
?>