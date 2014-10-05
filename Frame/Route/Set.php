<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Route;

/**
 * Set of Routes defined and cached for looking up as needed
 */
class Set implements \Iterator
{
  /**
   * The list of route objects
   *
   * @var array
   */
  protected $routes;

  /**
   * Initilizes the List object
   *
   * @param object
   */
  public function __construct()
  {
    $this->routes = array();
  }

  /**
   * Add a route object to the stack, indexed by the name of the route
   *
   * @param \Metrol\Frame\Route
   */
  public function addRoute(\Metrol\Frame\Route $route)
  {
    $this->routes[ $route->getName() ] = $route;
  }

  /**
   * Provide a given route be name.
   * Returns NULL if not found.
   *
   * @param string Name of route
   * @return \Metrol\Frame\Route
   */
  public function getRoute($routeName)
  {
    $rtn = null;

    if ( array_key_exists($routeName, $this->routes) )
    {
      $rtn = $this->routes[$routeName];
    }

    return $rtn;
  }

  /**
   * Report how many items we've got in here
   *
   * @return integer
   */
  public function count()
  {
    return count($this->routes);
  }

  /**
   * Reverses the order of all the routes in this set.
   */
  public function reverse()
  {
    $this->routes = array_reverse($this->routes, true);
  }

  /**
   * Implementing the Iterartor interface to walk through the routes
   */
  public function rewind()
  {
    reset($this->routes);
  }

  public function current()
  {
    return current($this->routes);
  }

  public function key()
  {
    return key($this->routes);
  }

  public function next()
  {
    return next($this->routes);
  }

  public function valid()
  {
    return $this->current() !== false;
  }
}
