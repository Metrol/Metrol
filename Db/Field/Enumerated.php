<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Field;

/**
 * Description of Boolean field
 */
class Enumerated
  extends \Metrol\Data\Type\Enumerated
  implements \Metrol\Db\Field
{
  /**
   * Initialize the Enumerated object
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Called when a record is setting a value
   * If the value isn't recognized, the default is to return FALSE
   *
   * @param boolean|string
   * @return boolean
   */
  public function setValue($value)
  {
    if ( $value === null and $this->nullOk )
    {
      return null;
    }
    else if ( $value === null and !$this->nullOk )
    {
      return $this->defaultValue();
    }

    if ( $this->isAllowed($value) )
    {
      return $value;
    }

    if ( !$this->isAllowed($value) and !$this->nullOk )
    {
      return $this->defaultValue();
    }
    else if ( !$this->isAllowed($value) and $this->nullOk )
    {
      return null;
    }
  }

  /**
   * Provide the SQL ready value of the input
   *
   * @param integer
   * @return integer
   */
  public function getSQLValue($value)
  {
    $rtn = $value;

    if ( $value === null and $this->nullOk )
    {
      return 'null';
    }
    else if ( $value === null and !$this->nullOk )
    {
      return $rtn = $this->defaultValue();
    }

    $rtn = "'".$rtn."'";

    return $rtn;
  }
}
