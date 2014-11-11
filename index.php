<?php

/*
 * El frontend controller se encarga de
 * configurar nuestra aplicacion
 */
require 'config/environment.php';

// Librerias del framework
require 'library/Assets.php';
require 'library/ErrorHandler.php';
require 'library/Inflector.php';
require 'library/Rest.php';
require 'library/ControllerResolver.php';
require 'library/Response.php';
require 'library/ResponseResolver.php';
require 'vendor/sosedoff/Router.php';

$router = new Router();

require 'config/routes.php';

try {

  $router->run();

} catch ( Exception $e ) {
  
  $errorHandler = new ErrorHandler( $e->getCode(), $e->getMessage() );

  $errorHandler->error();

}

?>