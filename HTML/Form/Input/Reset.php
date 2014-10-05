<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form\Input;

/**
 * Defines a Reset input tag
 */
class Reset extends \Metrol\HTML\Form\Input
{
  /**
   * Defines this as a Reset type of input tag.  Need to pass in the text
   * that will appear on the button.
   *
   * @param string
   */
  public function __construct($fieldName = null)
  {
    parent::__construct('reset');

    $this->setFieldName($fieldName);
  }
}
