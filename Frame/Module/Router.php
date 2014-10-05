<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Module;

/**
 * Based on the provided Request, this object will provide the appropriate
 * Controller Route for a Dispatch object.
 *
 */
class Router extends \Metrol\Frame\Router
{
  /**
   * Initializes the Router object
   *
   * @param \Metrol\Frame\Module\Request
   */
  public function __construct()
  {
    parent::__construct();
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
      print "Loading the module route set up!<br />\n";
      static::$routeSet = new \Metrol\Frame\Module\Route\Set;
    }
  }
}
