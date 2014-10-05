<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */
namespace Metrol;

/**
 * Provide the means to parse and store INI file information in singleton
 * class.
 */
class Preferences
{
  /**
   * Singleton object for this class
   *
   * @var Metrol\Preferences
   */
  private static $thisObj;

  /**
   * List of objects that have defined INI descriptions
   *
   * @var array
   */
  private static $iniObjects = array();

  /**
   * Private constructor for singleton pattern
   */
  private function __construct()
  {
    // A whole lotta nothin' goin' on.
  }

  public static function __callStatic($prefName, $arguments)
  {
    return self::getPref($prefName);
  }

  public static function getPref($prefName)
  {
    if ( array_key_exists($prefName, self::$iniObjects) ) {
      return self::$iniObjects[$prefName];
    }
  }

  /**
   * Add in a INI defined object to be referenced
   *
   * @param Metrol\Preferences\DefineINI
   */
  public static function addDefineINI(Preferences\DefineINI $def)
  {
    self::$iniObjects[$def->getName()] = $def;
  }
}
