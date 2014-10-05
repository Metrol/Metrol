<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form\Input;

/**
 * Defines a Submit input tag
 */
class Submit extends \Metrol\HTML\Form\Input
{
  /**
   * Defines a submit button
   *
   * @param string
   */
  public function __construct($fieldName = null)
  {
    parent::__construct('submit');

    $this->setFieldName($fieldName);
  }
}
