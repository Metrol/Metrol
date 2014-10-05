<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Event;

/**
 * An Event route, used to define which controllers and actions should be called
 * when an Event has been triggered.
 */
class Route extends \Metrol\Frame\Route
{
  /**
   * Initilizes the Route object
   *
   * @param string Name of the route. Same as the name of the Event.
   */
  public function __construct($routeName)
  {
    parent::__construct($routeName);
  }
}
