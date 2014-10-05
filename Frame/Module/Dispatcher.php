<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Module;

/**
 * Handles the request to enable a Module.
 *
 */
class Dispatcher extends \Metrol\Frame\Dispatcher
{
  /**
   * Initilizes the Dispatch object
   *
   * @param \Metrol\Frame\Event\Request
   */
  public function __construct(Request $request)
  {
    parent::__construct($request);
  }

  /**
   * Initializes the router that this object will ask how to handle requests
   *
   */
  protected function initRouter()
  {
    $this->router = new Router($this->request);
  }
}
