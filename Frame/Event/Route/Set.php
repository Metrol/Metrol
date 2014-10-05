<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Event\Route;

/**
 * Maintains a list of all the defined Event routes
 */
class Set extends \Metrol\Frame\Route\Set
{
  /**
   * Initilizes the Set object
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Add a route object to the stack, indexed by the name of the route.
   * Override the parent method so as to allow routes to merge with each other
   * rather than one replacing another.
   *
   * @param \Metrol\Frame\Route
   */
  public function addRoute(\Metrol\Frame\Route $route)
  {
    $rName = $route->getName();

    if ( array_key_exists($rName, $this->routes) )
    {
      $defRoute = $this->routes[$rName];

      $controllers = $route->getControllers();

      foreach ( $controllers as $cIdx => $controllerClass )
      {
        $actions = $route->getActions($cIdx);

        foreach ( $actions as $action )
        {
          $defRoute->addAction($controllerClass, $action);
        }
      }
    }
    else
    {
      $this->routes[$rName] = $route;
    }
  }
}
