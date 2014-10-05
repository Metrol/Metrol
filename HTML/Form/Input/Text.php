<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form\Input;

/**
 * Defines a Text input tag
 */
class Text extends \Metrol\HTML\Form\Input
{
  /**
   * Establish default values for size and max characters.
   *
   * @const
   */
  const DEF_CHARSIZE = 30;
  const DEF_MAXCHAR  = 30;

  /**
   * @param string Field name
   */
  public function __construct($fieldName = null)
  {
    parent::__construct('text');

    $this->setFieldName($fieldName);
    $this->setSize(self::DEF_CHARSIZE);
    $this->setMax(self::DEF_MAXCHAR);
  }

  /**
   * Sets the size of the input box
   *
   * @param integer Characters
   * @return \Metrol\HTML\Form\Text
   */
  public function setSize($chars)
  {
    $this->attribute()->size = intval($chars);

    return $this;
  }

  /**
   * Sets the maximum amount of characters allowed in the input box
   *
   * @param integer Characters
   * @return \Metrol\HTML\Form\Text
   */
  public function setMax($chars)
  {
    $this->attribute()->maxlength = intval($chars);

    return $this;
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
