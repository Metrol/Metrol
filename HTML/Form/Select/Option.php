<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form\Select;

/**
 * Define the Option tag that is included with the Select tag
 */
class Option extends \Metrol\HTML\Form\Tag
{
  /**
   * Instantiate the Option tag
   */
  public function __construct()
  {
    parent::__construct('option', self::CLOSE_CONTENT);
  }

  /**
   * Specifies if this option is selected on the list
   *
   * @param boolean
   * @return \Metrol\HTML\Option
   */
  public function setSelected($flag = true)
  {
    if ( $flag )
    {
      $this->attribute()->selected = 'selected';
    }
    else
    {
      $this->attribute()->delete('selected');
    }

    return $this;
  }
}
