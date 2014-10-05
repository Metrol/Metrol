<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Date;

/**
 * Used to produce a date block suitable for use in an iCal calendar
 *
 * Please refer to RFC 5545 for further details
 */
class iCal
{
  /**
   * Define the iCal version supported
   *
   * @const
   */
  const ICAL_VERSION = '2.0';

  /**
   * Specifies the identifier for the product that created the iCal object.
   * Defined in chapter 3.7.3 of RFC 5545.
   *
   * @var string
   */
  private $prodID;

  /**
   * A \Metrol\Date object that will define the start date and time
   *
   * @var \Metrol\Date
   */
  private $dateStart;

  /**
   * A \Metrol\Date object that will define the end date and time
   *
   * @var \Metrol\Date
   */
  private $dateEnd;

  /**
   * The summary or subject of the iCal entry
   *
   * @var string
   */
  private $summary;

  /**
   * A more detailed description of the entry
   *
   * @var string
   */
  private $description;

  /**
   * Determines whether or not to include a time zone identifier into the start
   * and end dates
   *
   * @param boolean
   */
  private $timeZoneIdentFlag;

  /**
   * Initilizes the iCal object
   */
  public function __construct()
  {
    $this->prodID      = '\\MetrolNet\\MetrolLib';
    $this->dateStart   = new \Metrol\Date();
    $this->dateEnd     = new \Metrol\Date();
    $this->summary     = '';
    $this->description = '';
    $this->timeZoneIdentFlag = false;

    $this->dateStart->useFormat('iCal');
    $this->dateEnd->useFormat('iCal');
  }

  /**
   * Wrapper for the output() method
   *
   * @return string
   */
  public function __toString()
  {
    return $this->output();
  }

  /**
   * Sets the product identifier
   *
   * @param string
   * @return this
   */
  public function setProductID($prodID)
  {
    $this->prodID = $prodID;

    return $this;
  }

  /**
   * Sets the summary/subject
   *
   * @param string
   * @return this
   */
  public function setSummary($summaryText)
  {
    $this->summary = $summaryText;

    return $this;
  }

  /**
   * Sets the detailed description of the entry
   *
   * @param string
   * @return this
   */
  public function setDescription($descText)
  {
    $this->description = $descText;

    return $this;
  }

  /**
   * Sets the start date/time for the entry
   *
   * @param \Metrol\Date
   * @return this
   */
  public function setStartDate(\Metrol\Date $date)
  {
    $this->dateStart = clone $date;
    $this->dateStart->useFormat('iCal');

    return $this;
  }

  /**
   * Sets the end date/time for the entry
   *
   * @param \Metrol\Date
   * @return this
   */
  public function setEndDate(\Metrol\Date $date)
  {
    $this->dateEnd = clone $date;
    $this->dateEnd->useFormat('iCal');

    return $this;
  }

  /**
   * Specifies whether or not to include the time zone identifier "TZID" with
   * the start and end dates.
   *
   * @param boolean
   * @return this
   */
  public function useTimeZoneIdent($flag)
  {
    if ( $flag )
    {
      $this->timeZoneIdentFlag = true;
    }
    else
    {
      $this->timeZoneIdentFlag = false;
    }
  }

  /**
   * Produces the output for this object
   *
   * @return string
   */
  public function output()
  {
    $rtn  = "BEGIN:VCALENDAR\n";

    $rtn .= 'VERSION:'.self::ICAL_VERSION."\n";
    $rtn .= 'PRODID:' .$this->prodID."\n";
    $rtn .= "METHOD:PUBLISH\n";

    $rtn .= "BEGIN:VEVENT\n";

    $stdt = '';
    $endt = '';

    if ( $this->timeZoneIdentFlag )
    {
      $stdt .= ';TZID='.$this->dateStart->getTimezone()->getName();
      $endt .= ';TZID='.$this->dateEnd->getTimezone()->getName();
    }

    $stdt .= ':'.$this->dateStart;
    $endt .= ':'.$this->dateEnd;

    $rtn .= 'DTSTART'  . $stdt ."\n";
    $rtn .= 'DTEND'    . $endt ."\n";

    if ( strlen($this->summary) > 0 )
    {
      $rtn .= 'SUMMARY:' . $this->summary   ."\n";
    }

    if ( strlen($this->description) > 0 )
    {
      $rtn .= 'DESCRIPTION:' . $this->description ."\n";
    }

    $rtn .= "END:VEVENT\n";

    $rtn .= "END:VCALENDAR\n";

    return $rtn;
  }
}
