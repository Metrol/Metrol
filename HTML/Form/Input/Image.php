<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form\Input;

/**
 * Image input type
 */
class Image extends \Metrol\Form\HTML\Input
{
  /**
   * Initialize the Image object
   *
   * @param string Name of the field
   */
  public function __construct($fieldName = null)
  {
    parent::__construct('image');

    $this->setFieldName($fieldName);
  }

  /**
   * Sets the alternate text attribute
   *
   * @param string
   * @return this
   */
  public function setAltText($text)
  {
    $this->attribute()->alt = htmlentities($text);

    return $this;
  }
}
