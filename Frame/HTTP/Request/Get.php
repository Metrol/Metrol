<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTTP\Request;

/**
 * Collects all the GET information
 */
class Get extends Query
{
  /**
   * Initiates the Get object
   */
  public function __construct()
  {
    parent::__construct($_GET);
  }
}
