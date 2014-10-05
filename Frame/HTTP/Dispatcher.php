<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTTP;

/**
 * Takes in requests from clients, passes those requests on to a Controller
 * based on what the Router specifies.  Can then provide a response back to
 * the client.
 * 
 */
class Dispatcher extends \Metrol\Frame\Dispatcher
{
  /**
   * Initilizes the Dispatch object
   *
   * @param \Metrol\Frame\HTTP\Request
   */
  public function __construct(Request $request)
  {
    parent::__construct($request);
  }

  /**
   * Override the parent method so as to put the response into the constructor.
   * Provides the Controller object to be called based on the class name
   * specified.
   *
   * @param string
   * @return \Metrol\Frame\HTTP\Controller
   */
  protected function initControllerObj($className)
  {
    $controller = new $className($this->request, $this->response);

    return $controller;
  }

  /**
   * Initialize the Response object
   *
   */
  protected function initResponse()
  {
    $this->response = new Response;
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
