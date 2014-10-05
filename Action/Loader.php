<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Action;

/**
 * Base class for the rest of the classes that load actions into the action
 * catalog
 *
 */
abstract class Loader
{
  /**
   * If a Controller prefix was specified, it gets stored here
   *
   * @var string
   */
  protected $controllerPrefix;

  /**
   * The action catalog that will be getting loaded
   *
   * @var \Metrol\Action\Catalog
   */
  protected $catalog;

  /**
   * Instantiates the Load object
   *
   */
  public function __construct()
  {
    $this->controllerPrefix = '';
    $this->initCatalog();
  }

  /**
   * Initialize the action catlog that will be getting loaded
   *
   */
  protected function initCatalog()
  {
    $this->catalog = \Metrol\Action\Catalog\Manager::getCatalog();
  }

  /**
   * The child object will need to implement this method
   *
   * @return this
   */
  abstract public function loadCatalog();


  /**
   * Does the work of parsing the INI file and filling in the Route Cache
   *
   * @return this
   */
  public function loadFromINI(\Metrol\File\INI $ini)
  {
    if ( isset($ini->controllerPrefix) )
    {
      $this->controllerPrefix = $ini->controllerPrefix;
      $ini->unsetField('controllerPrefix');
    }

    foreach ( $ini as $routeName => $ri )
    {
      if ( !isset($ri->controller) )
      {
        continue;
      }

      $route = $this->getNewRoute($routeName);
      $this->extractControllers($route, $ri);
      $this->moreRouteInfo($route, $ri);

      $this->cache->addRoute($route);
    }

    return $this;
  }

  /**
   * Handles extracting the controllers and actions from the routing
   * information.
   *
   * @param \Metrol\Frame\Route
   * @param array The route information
   */
  protected function extractControllers($route, $ri)
  {
    if ( is_array($ri->controller) )
    {
      $this->controllerList($route, $ri);
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
   * @param array Route info
   */
  protected function controllerSingle(\Metrol\Frame\Route $route, $ri)
  {
    $contClass = $ri->controller;

    if ( substr($contClass, 0, 1) != '\\' )
    {
      $contClass = $this->controllerPrefix.'\\'.$contClass;
    }

    if ( is_array($ri->action) )
    {
      foreach ( $ri->action as $action )
      {
        $route->addAction($contClass, $action);
      }
    }
    else
    {
      $route->addAction($contClass, $ri->action);
    }
  }

  /**
   * Deal with a list of controllers within a single route
   *
   * @param \Metrol\Frame\Route
   * @param array Route info
   */
  protected function controllerList(\Metrol\Frame\Route $route, $ri)
  {
    foreach ( $ri->controller as $cIdx => $controllerClass )
    {
      if ( substr($controllerClass, 0, 1) != '\\' )
      {
        $controllerClass = $this->controllerPrefix.'\\'.$controllerClass;
      }

      $actionKey = 'action.'.$cIdx;

      if ( !array_key_exists($actionKey, $ri) )
      {
        continue; // No action defined
      }

      if ( is_array($ri[$actionKey]) )
      {
        foreach ( $ri[$actionKey] as $action )
        {
          $route->addAction($controllerClass, $action);
        }
      }
      else
      {
        $route->addAction($controllerClass, $ri[$actionKey]);
      }
    }
  }

  /**
   * Meant to be overridden by a child class to load in more specific route
   * information
   *
   * @param \Metrol\Frame\Route $route
   * @param \Metrol\Data\Item $ri Route info
   */
  protected function moreRouteInfo(\Metrol\Frame\Route $route, \Metrol\Data\Item $ri)
  {
    return null;
  }
}
