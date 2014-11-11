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
class Route {
  public $is_matched = false;
  public $params;
  public $url;
  private $conditions;

  function __construct($url, $request_uri, $target, $conditions) {

  	$this->url = $url;
    $this->params = array();
    $this->conditions = $conditions;
    $p_names = array();
    $p_values = array();

    // extract pattern names (catches :controller, :action, :id, etc)
    preg_match_all('@:([\w]+)@', $url, $p_names, PREG_PATTERN_ORDER);
    $p_names = $p_names[0];

    // make a version of the request with and without the '?x=y&z=a&...' part
    $pos = strpos($request_uri, '?');
    if ($pos) {
      $request_uri_without = substr($request_uri, 0, $pos);
    } else {
      $request_uri_without = $request_uri;
    }

    foreach (array($request_uri, $request_uri_without) as $request) {
      $url_regex = preg_replace_callback('@:[\w]+@', array($this, 'regex_url'), $url);
      $url_regex .= '/?';

      if (preg_match('@^' . $url_regex . '$@', $request, $p_values)) {
        array_shift($p_values);
        foreach ($p_names as $index=>$value) {
          $this->params[substr($value, 1)] = urldecode($p_values[$index]);
        }
        foreach ($target as $key=>$value) {
          $this->params[$key] = $value;
        }
        $this->is_matched = true;
        break;
      }
    }

    unset($p_names);
    unset($p_values);
  }

  function regex_url($matches) {
    $key = str_replace(':', '', $matches[0]);
    if (array_key_exists($key, $this->conditions)) {
      return '(' . $this->conditions[$key] . ')';
    } else {
      return '([a-zA-Z0-9_\+\-%]+)';
    }
  }
}

?>