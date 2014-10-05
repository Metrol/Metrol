<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol;

/**
 * Extends the built in DateTime object to provide additional functionality
 *
 * Since the DateTime documentation is lacking, here are some of the methods
 * that will be inherited by this class.
 *
 * setDate(int $year, int $month, int $day)
 * setTime(int $hour, int $minute, int $seconds)
 * modify(string $modify)  strtotime() supported string
 * getOffset()
 * getTimezone()
 * setTimezone(DateTimeZone $timezone)
 */
class Date extends \DateTime
{
  /**
   * Establishes the default date formats
   *
   * @const
   */
  const DEF_FORMAT      = 'Y-m-d H:i:s';
  const DEF_DATE_FORMAT = 'Y-m-d';
  const DEF_TIME_FORMAT = 'H:i:s';

  /**
   * How the date will be formatted for output
   *
   * @var string
   */
  private $dateFormat;

  /**
   * An object that maintains a list of predefined date formats that can be
   * accessed by name.
   *
   * @var \Metrol\Date\Formats
   */
  private $formatRef;

  /**
   * Initializes the Date object
   *
   * @param string A date string supported by strtotime()
   * @param string Time zone string, as listed in time_zone_identifiers_list()
   * @throws InvalidArgumentException
   */
  public function __construct($dateStr = null, $timeZoneStr = "GMT")
  {
    if ( $dateStr === null )
    {
      $dateStr = gmdate(self::DEF_FORMAT);
    }

    parent::__construct($dateStr, new \DateTimeZone($timeZoneStr));

    if ( strlen($dateStr) > 0 and strtotime($dateStr) === FALSE )
    {
      throw new \InvalidArgumentException("Bad Date", 400);
    }

    $this->dateFormat = self::DEF_FORMAT;
    $this->formatRef  = new Date\Formats($this);
  }

  /**
   * The output of this object.
   *
   * @return string
   */
  public function __toString()
  {
    return $this->output();
  }

  /**
   * When cloned the formatRef needs to be initialized again to avoid glitches
   */
  public function __clone()
  {
    $this->formatRef = new Date\Formats($this);
  }

  /**
   * Provide the date back in the specified format
   *
   * @return string
   */
  public function output()
  {
    $rtn = trim($this->format($this->dateFormat));

    return $rtn;
  }

  /**
   * A quick time and date stamp suitable for database fields.  This always
   * returns the time and date of right now GMT.
   *
   * @return string
   */
  static public function timeDateStamp()
  {
    $stamp = gmdate(self::DEF_FORMAT);

    return $stamp;
  }

  /**
   * A quick date stamp suitable for database fields.  This always returns the
   * date of right now.
   *
   * @return string
   */
  static public function dateStamp()
  {
    $stamp = gmdate(self::DEF_DATE_FORMAT);

    return $stamp;
  }

  /**
   * The date format passed in here will be used the next time this object is
   * printed.
   * Refer to the PHP manual for a list of date formating options:
   * http://us3.php.net/manual/en/function.date.php
   *
   * @param string Date format
   * @return this
   */
  public function setFormat($dateFormat)
  {
    $this->dateFormat = $dateFormat;

    return $this;
  }

  /**
   * Set the date format to one of the defined formatting styles in
   * \Metrol\Date\Formats
   *
   * @param string Name of format
   * @return this
   */
  public function useFormat($formatName)
  {
    $this->formatRef->useDateFormat($formatName);

    return $this;
  }

  /**
   * Performs the same function as setTimezone, but takes a string argument
   * instead of a DateTimeZone object.
   *
   * @param string
   * @return this
   */
  public function setTimezoneString($timeZone)
  {
    $tzObj = new \DateTimeZone($timeZone);

    $this->setTimezone($tzObj);

    return $this;
  }

  /**
   * Just a quick way of changing the time zone to UTC
   *
   * @return this
   */
  public function setToUTC()
  {
    $this->setTimezone( new \DateTimeZone('UTC') );

    return $this;
  }

  /**
   * Provides which quarter the date stored here is in.
   *
   * @return integer
   */
  function quarter()
  {
    $dateString = $this->format(self::DEF_DATE_FORMAT);

    return (int)floor(date('m', strtotime($dateString)) / 3.1) + 1;
  }

  /**
   * Provides a GMT time stamp string suitable for use in most database engines.
   *
   * The value returned is always in the GMT time zone, not the previously set
   * one.  If you need a time stamp in a specific time zone, do not use this
   * method.  You have been warned!
   *
   * @param bool Whether or not to include time
   * @return string
   */
  public function sqlTimestamp($withTime = true)
  {
    if ($withTime)
    {
      $tzOrig = $this->getTimezone();
      $tzNew  = new DateTimeZone("GMT");

      $this->setTimezone($tzNew);
      $rtn = $this->format($this->formatRef->dateTimeSQL);

      $this->setTimezone($tzOrig);
    }
    else
    {
      $rtn = $this->format($this->formatRef->dateSQL);
    }

    return $rtn;
  }

  /**
   * Provides back what the time would be in the specified time zone, based on
   * the time already stored in this object.
   *
   * @param string
   * @param bool Should the returned time use AM/PM instead of 24 hour default
   * @return string hh:mm:ss 24 hour clock
   */
  public function timeInTZ($timeZone, $ampm = false)
  {
    $tzOrig = $this->getTimezone();
    $tzNew  = new DateTimeZone($timeZone);

    $this->setTimezone($tzNew);

    if ( $ampm )
    {
      $rtn = $this->format($this->formatRef->time);
    }
    else
    {
      $rtn = $this->format($this->formatRef->time24);
    }

    $this->setTimezone($tzOrig);

    return $rtn;
  }
}
