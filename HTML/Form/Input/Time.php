<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form\Input;

/**
 * Time input type
 */
class Time extends \Metrol\HTML\Form\Input
{
  /**
   * Initialize the Time object
   *
   * @param string Name of the field
   */
  public function __construct($fieldName = null)
  {
    parent::__construct('time');

    $this->setFieldName($fieldName);

    $this->attribute()->size = 7;
    $this->setStyle('text-align', 'right');
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
