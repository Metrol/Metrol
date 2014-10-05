<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Table;

/**
 * Specifies all the different settings that can be applied to a Table
 */
class Settings
{
  /**
   * Should the table make all the column widths even?
   *
   * @var boolean
   */
  private $evenColumnWidthsFlag;

  /**
   * The Table section will need to figure out what the column widths should
   * be if they are set to be even.  Here is where that value is stored.
   *
   * @var string
   */
  private $columnWidthVal;


  /**
   */
  public function __construct()
  {
    $this->evenColumnWidthsFlag = false;
  }

  /**
   *
   * @param boolean
   */
  public function setEventColumnWidths($flag)
  {
    if ( $flag )
    {
      $this->evenColumnWidthsFlag = true;
    }
    else
    {
      $this->evenColumnWidthsFlag = false;
    }

    return $this;
  }

  /**
   * Set the column width that will be applied to the table cells if the even
   * widths flag is enabled.
   *
   * @param string
   * @return \Metrol\HTML\Table\Settings
   */
  public function setColumnWidth($width)
  {
    $this->columnWidthVal = $width;
  }
}
