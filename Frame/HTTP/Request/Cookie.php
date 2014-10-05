<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTTP\Request;

/**
 * Collects all the COOKIE information
 */
class Cookie extends Query
{
  /**
   * Initiates the Cookie object
   */
  public function __construct()
  {
    parent::__construct($_COOKIE);
  }
}
