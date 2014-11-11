<?php

class ViewResponse extends Response {

  protected $template;
  protected $vars = array();

  public function __construct( $template, $vars = array() ) {

    $this->template = $template;
    $this->vars = $vars;
    
  }

  public function execute() {

    $template = $this->template;
    
    $vars = $this->vars;

    call_user_func(function () use ( $template, $vars ) {

      extract( $vars );

      ob_start();

      require "app/views/$template.tpl.php";

      $yield = ob_get_clean();

      require "app/views/layout/application.tpl.php";
      
    });
  }

}
?>