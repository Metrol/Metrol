<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form\Input;

/**
 * Defines a Radio tag
 */
class Radio extends \Metrol\HTML\Form\Input
{
  /**
   * Instantiate the radio object
   *
   * @param field name
   */
  public function __construct($fieldName = null)
  {
    parent::__construct('radio');

    $this->setFieldName($fieldName);
  }

  /**
   * Enables/Disables checking this radio
   *
   * @param boolean
   * @return this
   */
  public function setCheck($flag)
  {
    if ( $flag )
    {
      $this->attribute()->checked = 'checked';
    }
    else
    {
      $this->attribute()->delete('checked');
    }

    return $this;
  }
}
