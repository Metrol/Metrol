<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Data\Type;

/**
 * Describes numeric fields that store integer values.
 */
class Integer extends \Metrol\Data\Type
{
  /**
   * Bytes of storage allowed
   *
   * @var integer
   */
  protected $bytes;

  /**
   * Is the value signed or not.  When false, only positive values allowed
   *
   * @var boolean
   */
  protected $signed;

  /**
   * Instantiate the Integer description
   */
  public function __construct()
  {
    parent::__construct();

    $this->bytes  = 4;
    $this->signed = true;
  }

  /**
   * Defines the limit of what can be stored into an integer by the number of
   * bytes it can take.
   *
   * @param integer
   */
  public function setBytes($byteCount)
  {
    $this->bytes = intval($byteCount);
  }

  /**
   * Sets if the integer is signed, or is it always an absolute value.
   *
   * @param boolean
   */
  public function setSigned($flag)
  {
    if ( $flag )
    {
      $this->signed = true;
    }
    else
    {
      $this->signed = false;
    }
  }

  /**
   * Returns the integer value provided, or a zero if the value exceeds the
   * specified boundary.
   *
   * @param integer
   * @return integer
   */
  public function boundsValue($value)
  {
    $rtn = intval($value);

    // Can be a positive or negative value.
    if ( $this->signed )
    {
      if ( abs($rtn) > pow(256, $this->bytes) / 2 )
      {
        $rtn = 0;
      }
    }
    else // Only a postive value is allowed
    {
      $rtn = abs($rtn);

      if ( $rtn > pow(256, $this->bytes) )
      {
        $rtn = 0;
      }
    }

    return $rtn;
  }
}
