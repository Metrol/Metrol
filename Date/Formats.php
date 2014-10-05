<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Date;

/**
 * Defines various date and time formats suitable for the date() function
 */
class Formats
{
  /**
   * The date object that will use these formats
   *
   * @var \Metrol\Date
   */
  private $date;

  /**
   * Maintains a list of formatting styles that allow for commonly used
   * date formats by name.
   *
   * @var array
   */
  private $dateFormatList;

  /**
   * Initilizes the Formats object
   *
   * @param object
   */
  public function __construct(\Metrol\Date $date)
  {
    $this->date = $date;
    $this->dateFormatList = array();

    $this->initFormatStyles();
  }

  /**
   * A slightly easier way to get formatting styles out of this object
   *
   * @param string
   * @return string
   */
  public function __get($styleName)
  {
    $dateFormat = $this->getDateFormat($styleName);

    return $dateFormat;
  }

  /**
   * Provide a format string for the style name
   *
   * @param string
   * @return string
   */
  public function getDateFormat($styleName)
  {
    if ( !array_key_exists($styleName, $this->dateFormatList) )
    {
      return '';
    }

    $dateFormat = $this->dateFormatList[$styleName];

    return $dateFormat;
  }

  /**
   * Applies a named date format to the date object
   *
   * @param string
   */
  public function useDateFormat($styleName)
  {
    if ( !array_key_exists($styleName, $this->dateFormatList) )
    {
      return;
    }

    $dateFormat = $this->dateFormatList[$styleName];

    $this->date->setFormat($dateFormat);
  }

  /**
   * Adds a named format to the list of available defined date formats
   *
   * @param string Name of Format
   * @param string Date format
   */
  public function addDateFormat($styleName, $dateFormat)
  {
    $this->dateFormatList[$styleName] = $dateFormat;
  }

  /**
   * Puts together all the predefined named formats
   */
  private function initFormatStyles()
  {
    $this->addDateFormat("basic", "Y-m-d h:i:sa");          // 2008-09-03 7:30:00pm

    $this->addDateFormat("dateTimeSQL", "Y-m-d H:i:s");     // 2008-09-03 19:30:00
    $this->addDateFormat("dateTimeTZSQL", "Y-m-d H:i:s T"); // 2008-09-03 19:30:00 CDT
    $this->addDateFormat("dateSQL",     "Y-m-d");           // 2008-09-03
    $this->addDateFormat("veryShort", "Ymd");               // 20080903
    $this->addDateFormat("iCal",     "Ymd\THis ");          // 20080903T080000

    $this->addDateFormat("formal",        "F jS, Y");             // September 3rd, 2008
    $this->addDateFormat("formalMonthYear", "F Y");               // September 2008
    $this->addDateFormat("formalShort",   "M j, Y");              // Sep 3, 2008
    $this->addDateFormat("formalShortDay",   "M j");              // Sep 3
    $this->addDateFormat("formalShortTime",   "M jS, Y g:ia");    // Sep 3rd, 2008 7:30pm
    $this->addDateFormat("formalTime",    "F jS, Y g:ia");        // September 3rd, 2008 7:30pm
    $this->addDateFormat("formalTimeTZ",    "F jS, Y g:ia T");    // September 3rd, 2008 7:30pm CDT
    $this->addDateFormat("formalDay",     "l F jS, Y");           // Wednesday, September 3rd, 2008
    $this->addDateFormat("formalDayTime", "l F jS, Y g:ia");      // Wednesday, September 3rd, 2008 7:30pm
    $this->addDateFormat("formalDayTimeShort", "D M jS, Y g:ia"); // Wed, Sep 3rd, 2008 7:30pm
    $this->addDateFormat("formalDayShort", "D M jS, Y");          // Wed Sep 3rd, 2009
    $this->addDateFormat("formalDayMonthShort", "D, M d");        // Wed, Sep 3rd
    $this->addDateFormat("formalDayContract", "l, F j, Y");       // Wednesday, September 3, 2008

    $this->addDateFormat("shortDayDateTime", "D d-M-Y h:ia");  // Wed 03-Sep-2008 7:30PM
    $this->addDateFormat("shortDayDate", "D M-d-Y");           // Wed Sep-03-2008
    $this->addDateFormat("shortDayMonth", "d M");              // 03 Sep
    $this->addDateFormat("shortMonthYear", "M Y");             // Sep 2008
    $this->addDateFormat("fullDay", "l");                      // Wednedsay
    $this->addDateFormat("shortDay", "D");                     // Wed
    $this->addDateFormat("year", "Y");                         // 2009

    $this->addDateFormat("slash",       "m/d/Y ");       // 09/03/2008
    $this->addDateFormat("slashTime",   "m/d/Y g:ia");   // 09/03/2008 7:30pm
    $this->addDateFormat("slashTimeTZ", "m/d/Y g:ia T"); // 09/03/2008 7:30pm CDT
    $this->addDateFormat("slashTime24", "m/d/Y H:i");    // 09/03/2008 19:30

    $this->addDateFormat("military", "dMY");           // 03Sep2008
    $this->addDateFormat("militaryDash", "d-M-Y");     // 03-Sep-2008
    $this->addDateFormat("militaryTime", "dMY H:i:s"); // 03Sep2008 19:30:00

    $this->addDateFormat("time", "h:i:sa");            // 7:30:00pm
    $this->addDateFormat("timeShort", "h:ia");         // 7:30pm
    $this->addDateFormat("timeShortTZ", "h:ia");       // 7:30pm CDT
    $this->addDateFormat("time24", "H:i:s");           // 19:30:00
    $this->addDateFormat("time24Short", "H:i");        // 19:30
  }
}
