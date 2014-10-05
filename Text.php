<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol;

/**
 * Various customized text clean up utilities
 */
class Text
{
  const CHAR_ENCODE = 'UTF-8';

  /**
   * Converts all line breaks into HTML bullets
   * @return string
   */
  public static function bulletsHTML($text)
  {
    $br_Old = "<br />";
    $br_New = "<br />";
    $ls = "</li>\n<li>";

    $text = self::cleanEntities(); // Convert special characters
    $para = stripslashes($text);   // No left over back slashes
    $para = nl2br($para);          // Get them breaks in there

    $para = str_replace($br_Old, $ls, $para);
    $para = str_replace($br_New, $ls, $para);

    $result  = "<ul>\n";
    $result .= "<li>\n";
    $result .= "$para\n";
    $result .= "</li>\n";
    $result .= "</ul>\n";

    return $result;
  }

  /**
   * Takes plain text meant for HTML output and cleans it up.
   * Line breaks are converted to breaks, entities are fixed, and word
   * wrapping is applied.
   * @return string
   */
  public static function htmlentbrk($text)
  {
    $text = self::cleanEntities($text); // Convert special characters
    $para = stripslashes($text);        // No left over back slashes
    $para = nl2br($para);               // Get them breaks in there

    // For nice clean source code, wrap the text.  Deal with them break tags
    // that will wrap apart if we don't.
    $para = str_replace("<br />", "<br/>", $para); // So tags aren't split up
    $para = wordwrap($para);
    $para = str_replace("<br/>", "<br />", $para); // Now back to correct

    return $para;
  }

  /**
   * A multibyte safe replacement for htmlentities()
   *
   * @param string
   * @return string
   */
  public static function htmlent($text, $flags = ENT_COMPAT)
  {
    $text = self::charEncode($text);

    $rtn = htmlentities($text, $flags, self::CHAR_ENCODE);

    return $rtn;
  }

  /**
   * Be sure to properly convert the passed in text to the correct character
   * set.
   * @param string
   * @return string
   */
  public static function charEncode($text)
  {
    $text = strval($text); // Make sure we've got a string or mb_detect breaks
    $rtn = $text;

    if ( ! mb_detect_encoding($text, self::CHAR_ENCODE, true) )
    {
      $rtn = utf8_encode($text);
    }

    return $rtn;
  }

  /**
   * A clean replacment for htmlentities().
   * The standard PHP version of this will re-encode entities already on within
   * the text.  For those times when their very well may be entities already
   * in the text that are valid this can be safely used.
   * @return string
   */
  public static function cleanEntities($text)
  {
    // first replace all & with #%#
    $r = str_replace("&", "#%#", $text);

    // Now run the php function against it
    $r = self::htmlent($r);

    // Put all the old &'s back
    $r = str_replace("#%#", "&", $r);

    // Now look for any &'s that are all alone or have at least
    // one space next to them and make them entities as well.
    $r = str_replace("& ", "&amp; ", $r);

    // All done
    return $r;
  }

  /**
   * Converts MySQL formatted date to a Unix timestamp.
   *
   * @param string Your MySQL formatted date
   * @return integer Unix timestamp value
   */
  public static function getUnixDate($databaseDate)
  {
    if (strlen($databaseDate) > 10)
    {
      $dateParts = explode("-", substr($databaseDate, 0, 10));
      $timeParts = explode(":", substr($databaseDate, 11, 18));

      $unixDate = mktime($timeParts[0],$timeParts[1],$timeParts[2],
                         $dateParts[1],$dateParts[2],$dateParts[0]);
    }
    else
    {
      $dateParts = explode("-", substr($databaseDate, 0, 10));
      $unixDate = mktime(1, 0, 0, $dateParts[1],$dateParts[2],$dateParts[0]);
    }

    return $unixDate;
  }

  /**
   * A utility method for providing the suffix of a class name.
   *
   * This assumes that class names will be separated with "_", with the last
   * portion being a unique name.
   *
   * @param string Full class name
   * @return string Suffix of class name
   */
  public static function classNameSuffix($className)
  {
    $suffixDelimeter = "_";
    $suffixPosition = strrpos($className, $suffixDelimeter) + 1;

    // Only try the sub string if an underscore was found.
    if ( $suffixPosition > 1 )
    {
      $className = substr($className, $suffixPosition);
    }

    return $className;
  }

  /**
   * Takes in a block of text and hunts down any tid bits that exist between
   * the specified start and end delimiters.
   * This should be handy for things inside of "[ ]" or even "({" seperators.
   * The delimeters can be of any length.
   *
   * @param string The text to be walked through
   * @param string The starting bracket/delimeter
   * @param string the ending bracket/delimeter
   * @return array List of all the tid bits that were found
   */
  public static function parseBrackets($text, $startDelim, $endDelim)
  {
    $startPoint = 0;
    $foundFields = array();

    while ( strpos($text, $startDelim, $startPoint) !== FALSE )
    {
      $open = strpos($text, $startDelim, $startPoint) + strlen($startDelim);
      $close = strpos($text, $endDelim, $open);
      $foundFields[] = substr($text, $open, $close - $open);
      $startPoint = $close;
    }

    return $foundFields;
  }

  /**
   * Takes the integers in an array and turns them into a comma separated string
   *
   * @param array
   * @return string
   */
  public static function arrayToStr(array &$list)
  {
    $rtn = '';

    if ( count($list) > 0 )
    {
      foreach ( $list as $item )
      {
        $rtn .= $item.',';
      }

      $rtn = substr($rtn, 0, -1);
    }

    return $rtn;
  }
}
