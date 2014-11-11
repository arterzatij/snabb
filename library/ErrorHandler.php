<?php

class ErrorHandler {

  protected $code;
  protected $message;

  public function __construct( $code, $message ) {

    $this->code = $code;

    $this->message = $message;

  }

  public function error() {

  	$nr = $this->code; 
  	$message = $this->message; 

    $http_codes = array(
      404 => 'Not Found',
      500 => 'Internal Server Error',
      // we don't need the rest anyway ;-)
    );

    header($_SERVER['SERVER_PROTOCOL'] . " $nr {$http_codes[$nr]}");
    echo "
    <style type='text/css'>
      .routing-error { font-family:helvetica,arial,sans; border-radius:10px; border:1px solid #ccc; background:#efefef; padding:20px; }
      .routing-error h1 { padding:0px; margin:0px 0px 20px; line-height:1; }
      .routing-error p { color:#444; padding:0px; margin:0px; }
    </style>
    <div class='error routing-error'>
      <h1>Error $nr</h1>
      <p>$message</p>
    </div>";
    exit;
  }
}

?>