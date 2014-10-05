<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Event;

/**
 * When an Event is triggered a child of this class will be called to deal with
 * it.
 */
class Controller extends \Metrol\Frame\Controller
{
  /**
   * Initilizes the Controller object
   *
   * @param \Metrol\Frame\Event\Request
   */
  public function __construct(Request $request)
  {
    parent::__construct($request);
  }
}
