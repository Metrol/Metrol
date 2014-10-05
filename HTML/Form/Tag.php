<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form;

/**
 * The parent for all form related tags
 */
abstract class Tag extends \Metrol\HTML\Tag
{
  /**
   * Pass along the arguments to the parent Tag class to instantiate the tag
   *
   * @param string Name of the tag
   * @param integer Type of tag closure used, defined in the parent class
   */
  public function __construct($tagName, $closure)
  {
    parent::__construct($tagName, $closure);
  }

  /**
   * Set the field name of the input tag
   *
   * @param string The name attribute for a field object
   * @return this
   */
  public function setFieldName($fieldName)
  {
    $this->attribute()->name = $fieldName;

    return $this;
  }

  /**
   *  Set the value of the input tag
   *
   * @param string
   * @return this
   */
  public function setValue($val)
  {
    $this->attribute()->value = htmlentities($val);

    return $this;
  }

  /**
   * Get the value assigned to this object
   *
   * @return mixed
   */
  public function getValue()
  {
    return $this->attribute()->value;
  }

  /**
   * Specifies if the form object must have a value or not
   *
   * @param boolean
   * @return this
   */
  public function setRequired($flag = true)
  {
    if ( $flag )
    {
      $this->attribute()->required = 'required';
    }
    else
    {
      $this->attribute()->delete('required');
    }

    return $this;
  }

  /**
   * Enables the autofocus of a field
   *
   * @param boolean
   * @return this
   */
  public function setAutoFocus($flag = true)
  {
    if ( $flag )
    {
      $this->attribute()->autofocus = 'autofocus';
    }
    else
    {
      $this->attribute()->delete('autofocus');
    }

    return $this;
  }

  /**
   * Disable the tag
   *
   * @return this
   */
  public function disable()
  {
    $this->attribute()->disabled = 'disabled';

    return $this;
  }

  /**
   * Remove any disabled flags from the input tag
   *
   * @return this
   */
  public function enable()
  {
    $this->attribute()->delete('disabled');

    return $this;
  }
}
