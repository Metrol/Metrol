<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Event\Route;

/**
 * Description of Cache
 */
class Cache extends \Metrol\Frame\Route\Cache
{
  /**
   * The singleton instance of this class
   *
   * @var this
   */
  static protected $thisObj;

  /**
   * Initilizes the Cache object
   *
   * @param object
   */
  protected function __construct()
  {
    parent::__construct();
  }

  /**
   * Provide the one and only instance of this class
   *
   * @return \Metrol\Frame\Route\Manager
   */
  static public function getInstance()
  {
    if ( !is_object(static::$thisObj) )
    {
      $className = __CLASS__;
      static::$thisObj = new $className;
    }

    return static::$thisObj;
  }

  /**
   * Get the list of routes set together
   */
  protected function initRouteSet()
  {
    $this->routes = new Set;
  }
}
