<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTTP\Route;

/**
 * The HTTP Route Loader
 *
 */
class Loader extends \Metrol\Frame\Route\Loader
{
  /**
   * Initilizes the Load object
   *
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Initialize the route cache that we'll be using
   */
  protected function initCache()
  {
    $this->cache = Cache::getInstance();
  }

  /**
   * Provide a new route object
   *
   * @param string Name of the route
   *
   * @return \Metrol\Frame\Route
   */
  protected function getNewRoute($routeName)
  {
    $route = new \Metrol\Frame\HTTP\Route($routeName);

    return $route;
  }

  /**
   * Load in the HTTP specific parameters that should be found in the INI file
   * for the route
   *
   * @param \Metrol\Frame\Route
   * @param \Metrol\Data\Item Route info
   */
  protected function moreRouteInfo(\Metrol\Frame\Route $route, \Metrol\Data\Item $ri)
  {
    if ( isset($ri->match) )
    {
      $route->setMatch($ri->match);
    }

    if ( isset($ri->method) )
    {
      $route->setMethod($ri->method);
    }

    if ( isset($ri->status) )
    {
      $route->setStatus($ri->status);
    }

    if ( isset($ri->params) )
    {
      $route->setMaxParameters($ri->params);
    }

    if ( isset($ri->param) )
    {
      $route->setMaxParameters($ri->param);
    }

    if ( isset($ri->tagTitle) )
    {
      $route->setTagTitle($ri->tagTitle);
    }

    if ( isset($ri->pageTitle) )
    {
      $route->setPageTitle($ri->pageTitle);
    }
  }
}
