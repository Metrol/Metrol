<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form\Input;

/**
 * Defines a Hidden input tag
 */
class Hidden extends \Metrol\HTML\Form\Input
{
  /**
   *
   * @param string Field name
   */
  public function __construct($fieldName = null)
  {
    parent::__construct('hidden');

    $this->setFieldName($fieldName);
  }
}
