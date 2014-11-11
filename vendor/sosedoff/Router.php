<?php
/**
 * Rails like routing for PHP
 *
 * Based on http://blog.sosedoff.com/2009/09/20/rails-like-php-url-router/
 * but extended in significant ways:
 *
 * 1. Can now be deployed in a subdirectory, not just the domain root
 * 2. Will now call the indicated controller & action. Named arguments are
 *  converted to similarly method arguments, i.e. if you specify :id in the
 *  URL mapping, the value of that parameter will be provided to the method's
 *  '$id' parameter, if present.
 * 3. Will now allow URL mappings that contain a '?' - useful for mapping JSONP urls
 * 4. Should now correctly deal with spaces (%20) and other stuff in the URL
 *
 * @version 2.0
 * @author Dan Sosedoff <http://twitter.com/dan_sosedoff>
 * @author E. Akerboom <github@infostreams.net>
 */

require 'Route.php';

define('ROUTER_DEFAULT_CONTROLLER', 'home');
define('ROUTER_DEFAULT_ACTION', 'index');

class Router {
  
  public $request_uri;
  public $routes;
  public $params;
  public $route_found = false;

  protected $controllerResolver;
  protected $responseResolver;

  public function __construct() {
    $request = $this->get_request();

    $this->request_uri = $request;
    $this->routes = array();
  }
  
  public function map( $rule, $target = array(), $conditions = array() ) {

    if ( is_string( $target ) ) {
      // handle the shorthand notation "controller::action"
      list( $controller, $action ) = explode( '::', $target );

      $target = array( 'controller' => $controller, 'action' => $action );

    }
    
    $this->routes[$rule] = new Route( $rule, $this->request_uri, $target, $conditions );

  }

  public function run() {

    $this->match_routes();

    if ( $this->route_found ) { // we found a route!
            
      $response = $this->controllerResolver->executeController( $this->params );
      
      $this->responseResolver->executeResponse( $response );
    
    } else {

      throw new Exception( "Page not found", 404 );

    }

  }

  private function get_request() {

    $request_uri = rtrim($_SERVER["REQUEST_URI"], '/');
    
    // find out the absolute path to this script
    $here = realpath(rtrim(dirname($_SERVER["SCRIPT_FILENAME"]), '/'));
    $here = str_replace("\\", "/", $here . "/");

    // find out the absolute path to the document root
    $document_root = str_replace("\\", "/", realpath($_SERVER["DOCUMENT_ROOT"]) . "/");

    // let's see if we can return a path that is expressed *relative* to the script
    // (i.e. if this script is in '/sites/something/router.php', and we are
    // requesting /sites/something/here/is/my/path.png, then this function will 
    // return 'here/is/my/path.png')
    if (strpos($here, $document_root) !== false) {
      $relative_path = rtrim("/" . str_replace($document_root, "", $here), '/');
      $path_route = urldecode(str_replace($relative_path, "", $request_uri));
      return trim($path_route, '/');
    }

    // nope - we couldn't get the relative path... too bad! Return the absolute path
    // instead.
    return urldecode( $request_uri );

  }

  private function set_route( $route ) {

    $this->route_found = true;
    $params = $route->params;

    $controller = $params['controller']; 
    unset( $params['controller'] );

    if ( empty( $controller ) ) {
      $controller = ROUTER_DEFAULT_CONTROLLER;
    }
    

    $action = $params['action']; 
    unset( $params['action'] );

    if ( empty( $action ) ) {
      $action = ROUTER_DEFAULT_ACTION;
    }

    $this->params = $params;

    $this->controllerResolver = new ControllerResolver( $controller, $action );
    $this->responseResolver = new  ResponseResolver( $controller, $action );

  }

  private function match_routes() {

    foreach ($this->routes as $route) {

      if ( $route->is_matched ) {

        $this->set_route($route);
        break;

      }

    }

  }

}

?>