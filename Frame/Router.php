<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame;

/**
 * Acts as the conduit for all requests to load, fetch, or do anything with
 * routes.
 *
 */
class Router
{
  /**
   * A Request object from a client
   *
   * @var \Metrol\Frame\Request
   */
  protected $request;

  /**
   * The set of routes this object will manage
   *
   * @static \Metrol\Frame\Route\Set
   */
  protected static $routeSet;

  /**
   * Initializes the Router object
   *
   */
  public function __construct()
  {
    $this->request = null;

    $this->initRouteSet();
  }

  /**
   * Puts a new route on the stack
   *
   * @param \Metrol\Frame\Route $route
   */
  public function addRoute(\Metrol\Frame\Route $route)
  {
    self::$routeSet->addRoute($route);
  }

  /**
   * Provides back a route from the set based on the name
   *
   * @param string $routeName
   *
   * @return \Metrol\Frame\Route
   */
  public function getRoute($routeName)
  {
    return self::$routeSet->getRoute($routeName);
  }

  /**
   * Provide the entire route set this router manages
   *
   * @return \Metrol\Frame\Route\Set
   */
  public function getRouteSet()
  {
    return self::$routeSet;
  }

  /**
   * Sets the request object looking for a route
   *
   * @param \Metrol\Frame\Request $request
   *
   * @return this
   */
  public function setRequest(\Metrol\Frame\Request $request)
  {
    $this->request = $request;

    return $this;
  }

  /**
   * Initialize the route set as required
   *
   */
  protected function initRouteSet()
  {
    // This should only happen once per session.
    if ( !is_object(static::$routeSet) )
    {
      print "Loading the route set up!<br />\n";
      static::$routeSet = new \Metrol\Frame\Route\Set;
    }
  }
}
