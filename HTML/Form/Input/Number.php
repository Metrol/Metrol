<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form\Input;

/**
 * Number input type
 */
class Number extends Text
{
  /**
   * Initialize the Number object
   *
   * @param string Name of the field
   */
  public function __construct($fieldName = null)
  {
    parent::__construct($fieldName);

    $this->setInputType('number');
    $this->setSize(5);  // Default size
    $this->attribute()->maxlength = 10; // Default max characters
    $this->setStyle('text-align', 'right');
  }

  /**
   * Sets the maximum allowed value
   *
   * @param numeric
   * @return this
   */
  public function setMax($maxValue)
  {
    $this->attribute()->max = $maxValue;

    return $this;
  }

  /**
   * Sets the minimum allowed value
   *
   * @param numeric
   * @return this
   */
  public function setMin($minValue)
  {
    $this->attribute()->min = $minValue;

    return $this;
  }

  /**
   * Sets the allowed number intervals
   *
   * @param numeric
   * @return this
   */
  public function setStep($step)
  {
    $this->attribute()->step = $step;

    return $this;
  }
}
