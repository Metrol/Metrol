<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame;

/**
 * Used by applications to feed core information about what should be displayed
 */
class View
{
  /**
   * The response object that the view is responsible for putting data into
   *
   * @var \Metrol\Frame\Response
   */
  protected $response;

  /**
   * @param \Metrol\Response
   */
  public function __construct(Response $response)
  {
    $this->response = $response;
  }

  /**
   * Initiates putting together the view data and populates the response.
   */
  public function render()
  {
    $this->response->set('View Set');
  }
}
