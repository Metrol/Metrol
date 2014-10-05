<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame;

/**
 * Handles accepting a request, deciding what to do with it, then providing
 * a response.
 *
 */
class Controller
{
  /**
   * Keeps track of the request
   *
   * @var \Metrol\Frame\Request
   */
  protected $request;

  /**
   * The response object that will be sent back to whomever yanked this chain
   *
   * @var \Metrol\Frame\Response
   */
  protected $response;

  /**
   * Takes in the request and determines which specific kind of controller
   * is needed.
   *
   * @param \Metrol\Frame\Request
   */
  public function __construct(Request $req)
  {
    $this->request = $req;

    $this->initResponse();
  }

  /**
   * This is the default action that will be called if one is not specified by
   * the route.
   * 
   */
  public function defaultAction()
  {
    $this->response->set("I'm a default action");
  }

  /**
   * Used to set the Response object
   *
   * @param \Metrol\Frame\Response
   */
  public function setResponse(Response $response)
  {
    $this->response = $response;
  }

  /**
   * Initialize the Response object... just so it has something in there.
   *
   */
  protected function initResponse()
  {
    $this->response = new Response;
  }
}
