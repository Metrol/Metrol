<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form\Input;

/**
 * Defines a Password input tag
 * Since this is pretty much identical to a text input, extending it.
 */
class Password extends Text
{
  /**
   * @param field name
   */
  public function __construct($fieldName = null)
  {
    parent::__construct($fieldName);

    $this->setInputType('password');
  }

  /**
   * Disable a browser's auto complete for the password entry
   *
   * @return \Metrol\HTML\Form\Password
   */
  public function disableAutoComplete()
  {
    $this->attribute()->autocomplete = 'off';

    return $this;
  }

  /**
   * Removes the autocomplete off attribute
   *
   * @return \Metrol\HTML\Form\Password
   */
  public function enableAutoComplete()
  {
    $this->attribute()->delete('autocomplete');

    return $this;
  }
}
