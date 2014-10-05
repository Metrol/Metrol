<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form;

/**
 * Define the label tag
 */
class Label extends \Metrol\HTML\Tag
{
  /**
   * Specify the name of the field this is a label for
   *
   * @param string Form field this is a label for
   */
  public function __construct($fieldFor = null)
  {
    parent::__construct('label', self::CLOSE_CONTENT);

    $this->setField($fieldFor);
  }

  /**
   * Sets the field this is a label for
   *
   * @param string
   * @return this
   */
  public function setField($field)
  {
    $this->attribute()->for = $field;

    return $this;
  }
}
