<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Route;

/**
 * Parent class for the various kinds of route loading
 *
 */
abstract class Load
{
  /**
   * If a Controller prefix was specified, it gets stored here
   *
   * @var string
   */
  protected $controllerPrefix;

  /**
   * The router object managing the routes
   *
   * @var \Metrol\Frame\Router
   */
  protected $router;

  /**
   * Initilizes the Load object
   *
   */
  public function __construct()
  {
    $this->controllerPrefix = '';

    $this->initRouter();
  }

  /**
   * Does the work of parsing the INI file and filling in the Route Cache
   *
   * @return this
   */
  abstract public function load();

  /**
   * Determines if the route information specifies a controller, or multiple
   * controllers.  It then delegates the job of creating actions and populating
   * the route accordingly.
   *
   * @param \Metrol\Frame\Route
   * @param array The route information
   */
  protected function populateActions(\Metrol\Frame\Route $route, array $ri)
  {
    // Insure a controller and an action are specified in the route info before
    // doing anything
    if ( !$ri->isFieldSet('controller') or !$ri->isFieldSet('action') )
    {
      return null;
    }

    if ( is_array($ri->controller) )
    {
      $this->controllerSet($route, $ri);
    }
    else
    {
      $this->controllerSingle($route, $ri);
    }

    return $route;
  }

  /**
   * Deal with a single controller within a route
   *
   * @param \Metrol\Frame\Route
   * @param \Metrol\ Route info
   */
  protected function controllerSingle(\Metrol\Frame\Route $route, array $ri)
  {
    $action = new \Metrol\Frame\Route\Action;

    $contClass = $ri->controller;

    // Add on the controller prefix only if there isn't a leading backlash
    if ( substr($contClass, 0, 1) != '\\' )
    {
      $contClass = $this->controllerPrefix.'\\'.$contClass;
    }

    $action->setClass($contClass);

    if ( is_array($ri->action) )
    {
      foreach ( $ri->action as $method )
      {
        $action->addMethod($method);
      }
    }
    else
    {
      $action->addMethod($ri->action);
    }

    $route->addAction($action);
  }

  /**
   * Deal with a list of controllers within a single route
   *
   * @param \Metrol\Frame\Route
   * @param \Metrol\Data\Item Route info
   */
  protected function controllerList(\Metrol\Frame\Route $route, $ri)
  {
    foreach ( $ri->controller as $cIdx => $controllerClass )
    {
      $action = new \Metrol\Frame\Route\Action;

      if ( substr($controllerClass, 0, 1) != '\\' )
      {
        $controllerClass = $this->controllerPrefix.'\\'.$controllerClass;
      }

      $action->setClass($controllerClass);

      $actionKey = 'action.'.$cIdx;

      if ( !array_key_exists($actionKey, $ri) )
      {
        continue; // No action defined
      }

      if ( is_array($ri[$actionKey]) )
      {
        foreach ( $ri[$actionKey] as $method )
        {
          $action->addMethod($method);
        }
      }
      else
      {
        $action->addMethod($ri[$actionKey]);
      }

      $route->addAction($action);
    }
  }

  /**
   * Initialize the router that will be managing all the routes to be loaded
   *
   */
  protected function initRouter()
  {
    $this->router = new \Metrol\Frame\Router;
  }
}
