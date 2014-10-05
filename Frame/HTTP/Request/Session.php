<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTTP\Request;

/**
 * Collects all the SESSION information
 */
class Session extends Query
{
  /**
   * Initiates the Session object
   */
  public function __construct()
  {
    parent::__construct($_SESSION);
  }
}
