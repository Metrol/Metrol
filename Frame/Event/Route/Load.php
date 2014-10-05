<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Event\Route;

/**
 * Description of Load
 */
class Load extends \Metrol\Frame\Route\Loader
{
  /**
   * Initilizes the Load object
   *
   * @param string File name
   */
  public function __construct($fileName)
  {
    parent::__construct($fileName);
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
   * @return \Metrol\Frame\Route
   */
  protected function getNewRoute($routeName)
  {
    $route = new \Metrol\Frame\Event\Route($routeName);

    return $route;
  }
}
