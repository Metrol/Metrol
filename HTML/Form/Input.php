<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form;

/**
 * Define the input form tag
 */
class Input extends Tag
{
  /**
   * Need to specify what kind of input tag is needed.
   *
   * @param string
   */
  public function __construct($inputType)
  {
    parent::__construct('input', self::CLOSE_SELF);

    $this->setInputType($inputType);
  }

  /**
   * Provides a way to set the type of input this is
   *
   * @param string
   * @return this
   */
  public function setInputType($type)
  {
    $this->attribute()->type = $type;

    return $this;
  }
}
