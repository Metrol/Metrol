<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTTP;

/**
 * Based on the provided Request, this object will provide the appropriate
 * Controller Route for a Dispatch object.
 *
 */
class Router extends \Metrol\Frame\Router
{
  /**
   * Default route to look for when a page isn't found
   *
   * @const
   */
  const ERR404_ROUTE = 'Error 404 Page';

  /**
   * Initilizes the Router object
   *
   * @param \Metrol\Frame\Request
   */
  public function __construct(Request $request)
  {
    parent::__construct($request);
  }

  /**
   * Based on the URL and the cached routes going to try to come up with a
   * route.
   *
   * @return \Metrol\Frame\HTTP\Route
   */
  public function getRoute()
  {
    // If a route was explicitly asked for, give it up
    if ( $this->request->route != null )
    {
      $route = $this->cache->getRoute($this->request->route);

      return $route;
    }

    $routes = $this->cache->getRoutes();
    $routes->reverse();

    $routeFound = null;

    foreach ( $routes as $route )
    {
      if ( $route->checkRequestMatch($this->request) )
      {
        $routeFound = $route;
        break;
      }
    }

    $routes->reverse();

    // If the route is not found, let's now see if we can find the error 404
    // route.  Pass it back, or throw an exception if even that doesn't exist.
    if ( $routeFound === null )
    {
      $routeFound = $this->cache->getRoute(self::ERR404_ROUTE);
    }

    if ( $routeFound === null )
    {
      throw new \Metrol\Exception('['.self::ERR404_ROUTE.'] Not Defined');
    }

    // Enable the following to display which route was selected
    // print $routeFound;

    // error_log("Route Found: ".$routeFound->getName());

    if ( $this->logRouteSelected )
    {
      error_log($routeFound->getName());
    }

    return $routeFound;
  }

  /**
   * Initialize the route cache that we'll be using
   *
   */
  protected function initCache()
  {
    $this->cache = Route\Cache::getInstance();
  }
}
