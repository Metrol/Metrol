<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form\Input;

/**
 * Date input type
 */
class Date extends \Metrol\HTML\Form\Input
{
  /**
   * Initialize the Date object
   *
   * @param string Name of the field
   */
  public function __construct($fieldName = null)
  {
    parent::__construct('date');

    $this->setFieldName($fieldName);
  }

  /**
   * Used to put some sample text into the content that vanishes when the
   * user clicks in
   *
   * @param string
   * @return this
   */
  public function setPlaceholder($text)
  {
    $this->attribute()->placeholder = htmlentities($text);

    return $this;
  }
}
