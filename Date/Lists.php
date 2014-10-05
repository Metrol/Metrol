<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Date;

/**
 * Provides various lists of date and time parts that can be used for forms or
 * filling out any other date related area.
 */
class Lists
{
  /**
   * Provides a time zone listing that is suitable for a drop down list
   *
   * @return array
   */
  static public function timeZones()
  {
    $tz = new DateTimeZone("GMT");
    $tzList = array();

    foreach ( $tz->listIdentifiers() as $tzName )
    {
      $tzList[$tzName] = $tzName;
    }

    return $tzList;
  }

  /**
   * Provides a list of hours suitable for use in a drop down box
   *
   * @return array
   */
  static public function hours()
  {
    $hl = array();

    for ( $i = 0; $i < 24; $i++ )
    {
      if ( $i == 0 )
      {
        $hour = "12 AM";
      }
      elseif ( $i < 12 )
      {
        $hour = sprintf("%02s AM", $i);
      }
      elseif ( $i == 12 )
      {
        $hour = sprintf("%02s PM", $i);
      }
      else
      {
        $j = $i - 12;
        $hour = sprintf("%02s PM", $j);
      }

      $hl[$i] = $hour;
    }

    return $hl;
  }

  /**
   * Provides a list of minutes suitable for use in a drop down box
   *
   * @param integer How many minutes to increment the list by
   * @return array
   */
  static public function minutes($increment = 1)
  {
    $increment = intval($increment);

    if ( $increment <= 0 OR $increment >= 59 )
    {
      $increment = 1;
    }

    $ml = array();

    for ( $i = 0; $i < 60; $i = $i + $increment )
    {
      $j = sprintf("%02s", $i);
      $ml[$j] = $j;
    }

    return $ml;
  }

  /**
   * Provides a list of months suitable for use in a drop down box
   *
   * @param boolean
   * @return array
   */
  static public function months($short = false)
  {
    $ml = array();

    if ( $short )
    {
      $monthFmt = "M";
    }
    else
    {
      $monthFmt = "F";
    }

    for ( $i = 1; $i <= 12; $i++ )
    {
      $timeStamp = mktime(0, 0, 0, $i, 1, 1999);
      $ml[$i] = date($monthFmt, $timeStamp);
    }

    return $ml;
  }

  /**
   * Just a list of day numbers
   *
   * @return array
   */
  static public function days($max = 31)
  {
    $dl = array();

    for ( $i = 1; $i <= $max; $i++ )
    {
      $dl[$i] = sprintf("%02s", $i);
    }

    return $dl;
  }

  /**
   * Provides a list of week days suitable for use in a drop down box
   *
   * @return array
   */
  static public function weekDays()
  {
    $wdl = array();

    for ($i = 1; $i <= 7; $i++) {
      $timeStamp = mktime(0, 0, 0, 8, $i, 1999);
      $weekDay = date("l", $timeStamp);
      $wdl[$i] = $weekDay;
    }

    return $wdl;
  }
}
