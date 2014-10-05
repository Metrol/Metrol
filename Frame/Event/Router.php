<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Event;

/**
 * Based on the provided Request, this object will provide the appropriate
 * Controller Route for a Dispatch object.
 */
class Router extends \Metrol\Frame\Router
{
  /**
   * Initilizes the Router object
   *
   * @param \Metrol\Frame\Event\Request
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
    $route = $this->cache->getRoute($this->request->getEventName());

    return $route;
  }

  /**
   * Initialize the route cache that we'll be using
   */
  protected function initCache()
  {
    $this->cache = Route\Cache::getInstance();
  }
}
