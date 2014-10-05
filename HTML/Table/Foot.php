<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Table;

/**
 * Defines an HTML Table Foot Area
 */
class Foot extends Section
{
  /**
   */
  public function __construct()
  {
    parent::__construct('tfoot');

    $this->rows = array();
  }
}
