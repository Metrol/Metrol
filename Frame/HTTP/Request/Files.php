<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTTP\Request;

/**
 * Collects all the FILE information
 */
class Files extends Query
{
  /**
   * Initiates the File object
   */
  public function __construct()
  {
    parent::__construct($_FILES);
  }
}
