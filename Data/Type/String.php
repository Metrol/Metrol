<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Data\Type;

/**
 * Defines a string object with specific boundaries for storage
 */
class String extends \Metrol\Data\Type
{
  /**
   * Max length.
   * Unlimited if null
   *
   * @var integer
   */
  protected $maxLength;

  /**
   * For text fields of a fixed length
   *
   * @var integer
   */
  protected $fixedLength;

  /**
   * Instantiate the String description
   */
  public function __construct()
  {
    parent::__construct();

    $this->maxLength   = null;
    $this->fixedLength = null;
  }

  /**
   * Makes sure the value being returned is within the specified character
   * length and attributes.
   *
   * @param string
   * @return string
   */
  public function boundsValue($value)
  {
    $rtn = strval($value);

    if ( $this->maxLength != null and $this->maxLength > 0 )
    {
      $rtn = trim(substr($rtn, 0, $this->maxLength));
    }

    if ( $this->fixedLength != null and $this->fixedLength > 0 )
    {
      $rtn = substr($rtn, 0, $this->fixedLength);
      $len = strlen($rtn);

      if ( $len < $this->fixedLength )
      {
        $rtn .= str_repeat(' ', $this->fixedLength - $len);
      }
    }

    return $rtn;
  }

  /**
   * Sets the maximum length allowed for the string
   *
   * @param integer
   */
  public function setMaxLength($length)
  {
    if ( intval($length) > 0 )
    {
      $this->maxLength = intval($length);
    }
  }

  /**
   * Sets the maximum length allowed for the string
   *
   * @param integer
   */
  public function setFixedLength($length)
  {
    if ( intval($length) > 0 )
    {
      $this->maxLength   = intval($length);
      $this->fixedLength = intval($length);
    }
  }
}
