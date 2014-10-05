<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Data\Type;

/**
 * Describes numeric fields that store decimal values.
 */
class Numeric extends \Metrol\Data\Type
{
  /**
   * How many digits of precision used
   *
   * @var integer
   */
  protected $precision;

  /**
   * The scale of decimal places used
   *
   * @var integer
   */
  protected $decScale;

  /**
   * Instantiate the Numeric description
   */
  public function __construct()
  {
    parent::__construct();

    $this->precision  = 10;
    $this->decScale   = 4;
  }

  /**
   * Set the precision of this type
   *
   * @param integer
   */
  public function setPrecision($digits)
  {
    $this->precision = intval($digits);
  }

  /**
   * Set the scale, defining the number of digits to the right of the decimal
   *
   * @param integer
   */
  public function setScale($digits)
  {
    $this->scale = intval($digits);
  }

  /**
   * Make sure we're within the boundaries defined
   *
   * @param numeric
   * @return numeric
   */
  public function boundsValue($value)
  {
    $rtn = round(floatval($value), $this->scale);

    if ( $rtn > pow(10, $this->precision - $this->scale) - 1)
    {
      $rtn = 0;
    }

    return $rtn;
  }

}