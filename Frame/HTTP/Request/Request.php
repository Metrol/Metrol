<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTTP\Request;

/**
 * Collects all the REQUEST query information
 */
class Request extends Query
{
  /**
   * Initiates the Request object
   */
  public function __construct()
  {
    parent::__construct($_REQUEST);
  }
}
