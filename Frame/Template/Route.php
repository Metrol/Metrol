<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Template;

/**
 * Adds routing functionality to the Twig templating engine
 */
class Route extends \Twig_Extension
{
  /**
   * Reference to the list of HTTP routes that should already be loaded
   *
   * @var \Metrol\Frame\HTTP\Route\Cache
   */
  protected $routes;

  /**
   * Initialize the Route object
   */
  public function __construct()
  {
    $this->routes = \Metrol\Frame\HTTP\Route\Cache::getInstance();
  }

  /**
   * Name of this Twig extension
   *
   * @return string
   */
  public function getName()
  {
    return 'MetrolRoute';
  }

  /**
   * Add the routing functions to the stack
   *
   * @return array
   */
  public function getFunctions()
  {
    $rtn = array();

    $routes = $this->routes;

    $fetch = function($routeName) use ($routes)
    {
      $args = func_get_args();
      array_shift($args);

      $route = $routes->getRoute($routeName);
      $route->clearArguments();

      foreach ( $args as $arg )
      {
        $route->addArguments($arg);
      }

      return $route->getURL();
    };

    $rtn[] = new \Twig_SimpleFunction('path', $fetch);

    return $rtn;
  }
}
