<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form\Input;

/**
 * URL input type
 */
class URL extends Text
{
  /**
   * Initialize the URL object
   *
   * @param string Name of the field
   */
  public function __construct($fieldName = null)
  {
    parent::__construct($fieldName);

    $this->setInputType('url');
  }
}
