<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form\Input;

/**
 * Defines a Button input tag
 */
class Button extends \Metrol\HTML\Form\Input
{
  /**
   * Defines this as a Button type of input tag.  Need to pass in the text
   * that will appear on the button.
   *
   * @param string
   */
  public function __construct($fieldName = null)
  {
    parent::__construct('button');

    $this->setFieldName($fieldName);
  }

  /**
   * Define the onClick() DOM Event for this button
   *
   * @param string JS Function
   * @return this
   */
  public function setOnClick($js)
  {
    $this->attribute()->onclick = $js;

    return $this;
  }
}
