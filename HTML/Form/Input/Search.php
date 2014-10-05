<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form\Input;

/**
 * Search input type
 */
class Search extends Text
{
  /**
   * Initialize the Search object
   *
   * @param string Name of the field
   */
  public function __construct($fieldName = null)
  {
    parent::__construct($fieldName);

    $this->setInputType('search');
  }
}
