<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame;

/**
 * Description of Response
 */
class Response
{
  /**
   * The response that will be sent back to a request
   *
   * @var string
   */
  protected $resp;

  /**
   * Initilizes the Response object
   *
   * @param object
   */
  public function __construct()
  {
    $this->resp = '';
  }

  /**
   * Dumps the response value out as a string
   *
   * @return string
   */
  public function __toString()
  {
    return strval($this->resp);
  }

  /**
   * Provide the response string
   *
   * @return object
   */
  public function get()
  {
    return $this->resp;
  }

  /**
   * Set the response string
   *
   * @param string
   */
  public function set($response)
  {
    $this->resp = strval($response);
  }

  /**
   * Add to the response string
   *
   * @param string
   */
  public function add($response)
  {
    $this->resp .= strval($response);
  }
}
