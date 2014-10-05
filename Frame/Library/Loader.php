<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Library;
use Metrol\Autoload;

/**
 * Keeps track of all the libraries that have been loaded for use
 *
 */
class Loader
{
  /**
   * List of Library objects
   *
   * @var array
   */
  static protected $libs = array();

  /**
   * Not meant to become an object after all
   *
   */
  private function __construct()
  {
    // Nothing to do, nowhere to go
  }

  /**
   * Diagnostic output listing the libraries loaded
   *
   * @return string
   */
  static public function debug()
  {
    $rtn = "Libraries Loaded\n";
    $rtn .= "---------------------------------------\n";

    foreach ( self::$libs as $library )
    {
      $rtn .= $library."\n\n";
    }

    return nl2br($rtn);
  }

  /**
   * Adds a library to the stack
   *
   * @param \Metrol\Frame\Library
   */
  static public function addLibrary(\Metrol\Frame\Library $lib)
  {
    if ( array_key_exists($lib->name, self::$libs) )
    {
      return;
    }

    if ( !$lib->enabled )
    {
      return;
    }

    self::$libs[$lib->name] = $lib;

    if ( $lib->includePath )
    {
      Autoload::addIncludePath($lib->path);
    }

    if ( strlen($lib->prefix) > 0 and strlen($lib->path) > 0 )
    {
      Autoload::addLibrary($lib->prefix, $lib->path);
    }
  }

  /**
   * Loads up the list of libraries from the specified INI object
   *
   * @param \Metrol\File\INI
   */
  static public function loadFromINI(\Metrol\File\INI $ini)
  {
    $ini->parse(); // Make sure the INI has parsed

    // Begin with initiliazing the library objects and storing them
    foreach ( $ini as $libName => $libData )
    {
      // Don't bother for lib name that's a dupe
      if ( array_key_exists($libName, self::$libs) )
      {
        continue;
      }

      // Skip the library if not enabled
      if ( !$libData->enabled )
      {
        continue;
      }

      $lib = new \Metrol\Frame\Library($libName);

      $lib->description = $libData->description;
      $lib->prefix      = $libData->prefix;
      $lib->enabled     = true; // default up front
      $lib->includePath = false;
      $lib->path        = $libData->path;

      if ( $libData->includepath )
      {
        $lib->includePath = true;
      }

      self::addLibrary($lib);
    }
  }
}
