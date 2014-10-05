<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Field;

/**
 * Provide type, bounds, and formatting for a Date or Time field
 */
class DateTime
  extends \Metrol\Data\Type\DateTime
  implements \Metrol\Db\Field
{
  /**
   * Stores the actual database specified field type so a proper default date
   * format can be selected.
   *
   * @var string
   */
  protected $dbDateType;

  /**
   * Initialize the String object
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Set the date type as the database actually reports it so the appropriate
   * format is chosen by default
   *
   * @param string
   */
  public function setDbDateType($fieldType)
  {
    $this->dbDateType = $fieldType;
  }

  /**
   * Called when a Record is setting a date member
   *
   * @param \Metrol\Db\Field\Date | string
   * @return \Metrol\Date
   */
  public function setValue($value)
  {
    if ( $value === null and $this->nullOk )
    {
      return null;
    }
    else if ( $value === null and !$this->nullOk )
    {
      $value = new \Metrol\Date;
    }

    if ( is_object($value) )
    {
      if ( $value instanceOf \Metrol\Date )
      {
        return $value;
      }
    }

    $dt = new \Metrol\Date($value);

    $this->setDefaultFormat($dt);

    return $dt;
  }

  /**
   * I'm hoping to do more with this later, for now just let things pass on
   * through.
   */
  public function boundsValue($value)
  {
    return $value;
  }

  /**
   * Provide a properly quoted and escaped representation of the data ready for
   * putting into an SQL statement.
   *
   * @param \Metrol\Date
   * @return string
   */
  public function getSQLValue($date)
  {
    if ( $date === null and $this->nullOk )
    {
      return 'null';
    }
    else if ( $date === null and !$this->nullOk )
    {
      $date = new \Metrol\Date;
    }
    else if ( !is_object($date) )
    {
      $date = new \Metrol\Date($date);
    }

    $cpDt = clone $date;
    $cpDt->setToUTC();
    $this->setSQLFormat($cpDt);

    $rtn = "'".$cpDt->output()."'";

    return $rtn;
  }

  /**
   * Sets a date format suitable for display into the provided date object
   *
   * @param \Metrol\Date
   */
  private function setDefaultFormat(\Metrol\Date $dt)
  {
    switch ($this->dbDateType)
    {
      case 'date':
        $dt->setFormat('F d, Y');
        break;

      case 'timestamp without time zone':
        $dt->setFormat('F d, Y h:i a');
        break;

      case 'timestamp with time zone':
        $dt->useFormat('F d, Y h:i a T');
        break;

      case 'time without time zone':
        $dt->setFormat('h:i a');
        break;

      case 'time with time zone':
        $dt->setFormat('h:i a T');
        break;

      default:
        break;
    }
  }

  /**
   * Sets a date format suitable for inserting into the database
   *
   * @param \Metrol\Date
   */
  private function setSQLFormat(\Metrol\Date $dt)
  {
    switch ($this->dbDateType)
    {
      case 'date':
        $dt->useFormat('dateSQL');
        break;

      case 'timestamp without time zone':
        $dt->useFormat('dateTimeSQL');
        break;

      case 'timestamp with time zone':
        $dt->useFormat('dateTimeTZSQL');
        break;

      case 'time without time zone':
        $dt->useFormat('timeShort');
        break;

      case 'time with time zone':
        $dt->useFormat('timeShortTZ');
        break;

      default:
        break;
    }
  }
}
