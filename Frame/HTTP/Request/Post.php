<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTTP\Request;

/**
 * Collects all the POST information
 */
class Post extends Query
{
  /**
   * Initiates the Get object
   */
  public function __construct()
  {
    parent::__construct($_POST);
  }
}
