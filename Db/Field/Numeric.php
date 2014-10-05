<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Field;

/**
 * Description of a Numeric field
 */
class Numeric
  extends \Metrol\Data\Type\Numeric
  implements \Metrol\Db\Field
{
  /**
   * Initialize the Integer object
   *
   * @param object
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Called when a record is setting a value
   *
   * @param integer
   * @return integer
   */
  public function setValue($value)
  {
    if ( $value === null and $this->nullOk )
    {
      return null;
    }

    return $this->boundsValue($value);
  }

  /**
   * Provide the SQL ready value of the input
   *
   * @param numeric
   * @return numeric
   */
  public function getSQLValue($value)
  {
    if ( $value === null and $this->nullOk )
    {
      return 'null';
    }
    else if ( $value === null and !$this->nullOk )
    {
      return 0;
    }

    $rtn = $this->boundsValue($value);

    return $rtn;
  }
}
