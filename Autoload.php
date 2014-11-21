<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol;

/**
 * Provide class autoloading
 */
class Autoload
{
  /**
   * List of expected file suffixes to look for.
   *
   * @var array
   */
  private static $suffixes = array();

  /**
   * List of directories to look for files in
   *
   * @var array
   */
  private static $paths = array();

  /**
   * List of libraries and their prefixes to specify where to find them.
   * When the prefix is found in the class name, this will take precedence
   * over the paths list.
   * Fmt: arr['Prefix'] = '/path/'
   *
   * @var array
   */
  private static $libPaths = array();

  /**
   * List of classes already loaded, so as to avoid looking to load any twice
   *
   * @var array
   */
  private static $loaded = array();

  /**
   * A flag to determine if this class has already been registered as the
   * default autoloader.
   *
   * @var boolean
   */
  private static $isRegistered = false;

  /**
   * When a problem occurs with loading a class diagnostic information will
   * go into this variable.
   *
   * @var array
   */
  private static $backTrace = array();

  /**
   * Prevent any ability to create an object from this.
   */
  private function __construct()
  {
    // Nothing to do, no where to goto
  }

  /**
   * Registers this class as the handler for auto loading classes on demand
   *
   * @throws \Metrol\Autoload\Exception
   */
  public static function register()
  {
    if ( self::$isRegistered ) { return; }

    $res = spl_autoload_register('Metrol\Autoload::load');

    if ( !$res )
    {
      throw new Autoload\Exception("Unable to register autoloader");
    }

    self::$isRegistered = true;
  }

  /**
   * Adds a library path to specify where to find classes that begin with a
   * specific prefix.
   *
   * @param string
   * @param string
   */
  public static function addLibrary($prefix, $path)
  {
    self::$libPaths[$prefix] = $path;
  }

  /**
   * Removes a library from the autoloader
   *
   * @param string
   */
  public static function removeLibrary($prefix)
  {
    if ( array_key_exists($prefix, self::$libPaths) )
    {
      unset(self::$libPaths[$prefix]);
    }
  }

  /**
   * Add a new directory to the search path for classes.
   *
   * @param string
   */
  public static function addPath($path)
  {
    self::$paths[] = $path;
  }

  /**
   * Adds a new entry to the PHP include path
   *
   * @param string
   */
  public static function addIncludePath($path)
  {
    set_include_path(get_include_path() . PATH_SEPARATOR . $path);
  }

  /**
   * Add a new suffix to the search list
   *
   * @param string
   */
  public static function addSuffix($suffix)
  {
    if ( strlen($suffix) < 2 )
    {
      return;
    }

    // Make darn sure the suffix has a period as the first char
    if ( substr($suffix, 0, 1) != "." )
    {
      $suffix = '.'.$suffix;
    }

    // See if it's already in the list
    if ( in_array($suffix, self::$suffixes) )
    {
      return;
    }

    self::$suffixes[] = $suffix;
  }

  /**
   * Called by the PHP autoloader to pull in classes on demand
   *
   * @param string
   * @throws Exception
   */
  public static function load($className)
  {
    if ( strlen($className) < 2 )
    {
      return FALSE;
    }

    if ( in_array($className, self::$loaded) )
    {
      return; // Already loaded
    }

    if ( class_exists($className, FALSE) )
    {
      return; // Already loaded
    }

    try
    {
      $fileToLoad = self::classFileName($className);
    }
    catch ( Autoload\Exception $e )
    {
      $e->addToMsg(self::debug());

      throw $e;
    }

    if ( !is_readable($fileToLoad) )
    {
      $msg  = "<h1>Class Permission Error: $className</h1>";
      $msg .= '<p>Check the permissions on:<br />';
      $msg .= $fileToLoad.'</p>';

      exit;
    }

    self::$loaded[] = $className;

    require $fileToLoad;
  }

  /**
   * Determines the file name and path of a class name
   *
   * @param string $className
   *
   * @return string
   *
   * @throws \Metrol\Autoload\Exception
   */
  public static function classFileName($className)
  {
    $classExplode = self::explodeClassName($className);
    $rtnFileName  = '';

    if ( count($classExplode) == 0 )
    {
      throw new Autoload\Exception('Unable to parse class: '.$className);
    }

    $libFileName = self::checkLibraries($className);

    if ( strlen($libFileName) > 0 )
    {
      $rtnFileName = $libFileName;
    }

    $pathFileName = self::checkPaths($className);

    if ( strlen($pathFileName) > 0 )
    {
      $rtnFileName = $pathFileName;
    }

    if ( strlen($rtnFileName) == 0 )
    {
      self::$backTrace = debug_backtrace();
      throw new Autoload\Exception('File Not Found for class: '.$className);
    }

    return $rtnFileName;
  }

  /**
   * Looks to see if the specified class is from a library.  If so, return
   * the name of the file to load.
   *
   * @param string Class name
   * @return string Full file name with path
   */
  private static function checkLibraries($className)
  {
    $ds           = DIRECTORY_SEPARATOR;
    $classExplode = self::explodeClassName($className);
    $fileName     = ''; // The file name of the class
    $rtnFileName  = ''; // File with the full path to be returned

    foreach ( $classExplode as $classPart )
    {
      $fileName .= $classPart.$ds;
    }

    // Strip off the final directory separator
    $fileName = substr($fileName, 0, -1);

    // First check to see if the requested file is one from a defined library
    if ( array_key_exists($classExplode[0], self::$libPaths) )
    {
      $libName = $classExplode[0];
      $fileName = self::$libPaths[$libName].$ds.$fileName;

      // Look for the possible suffixes in the defined directory
      foreach ( self::$suffixes as $suffix )
      {
        $testFile = $fileName.$suffix;

        if ( file_exists($testFile) )
        {
          $rtnFileName = $testFile;
          break;
        }
      }
    }

    return $rtnFileName;
  }

  /**
   * Looks for the specified class in the paths that have been added to this
   * class.
   *
   * @param string Class name
   * @return string Full file name with path
   */
  private static function checkPaths($className)
  {
    $ds           = DIRECTORY_SEPARATOR;
    $classExplode = self::explodeClassName($className);
    $fileName     = ''; // The file name of the class
    $rtnFileName  = ''; // File with the full path to be returned

    foreach ( $classExplode as $classPart )
    {
      $fileName .= $classPart.$ds;
    }

    // Strip off the final directory separator
    $fileName = substr($fileName, 0, -1);

    // Walk through each possible file name suffix and check in each file
    // location.  Break out once found.
    foreach ( self::$suffixes as $suffix )
    {
      foreach ( self::$paths as $path)
      {
        $testFile = $path.$ds.$fileName.$suffix;

        // print $testFile."<br />\n";

        if ( file_exists($testFile) )
        {
          $rtnFileName = $testFile;
          break 2;
        }
      }
    }

    return $rtnFileName;
  }

  /**
   * Take apart the name of the class into items in an array based on likely
   * word delimiters used.
   *
   * @param string
   * @return array
   */
  private static function explodeClassName($className)
  {
    $delimiters = array('\\', '_');
    $delimiter = '';

    foreach ( $delimiters as $d )
    {
      if ( strpos($className, $d) !== false )
      {
        $delimiter = $d;
        break;
      }
    }

    if ( strlen($delimiter) == 0 )
    {
      return array($className);
    }

    $parts = explode($delimiter, $className);

    return $parts;
  }

  /**
   * Dumps out the list of suffixes, paths, libPaths and what has already been
   * loaded.
   *
   * @return string
   */
  public static function debug()
  {
    $rtn = '';

    $rtn .= "AutoLoader Debug Output\n";
    $rtn .= "Has been Registered: ";

    if ( self::$isRegistered )
    {
      $rtn .= "True \n";
    }
    else
    {
      $rtn .= "False \n";
    }

    $rtn .= "\n";
    $rtn .= "PHP's Include Search Path:\n";
    $rtn .= get_include_path();

    $rtn .= "\n\n";
    $rtn .= "Defined Suffixes:\n";

    if ( count(self::$suffixes) == 0 )
    {
      $rtn .= "-- None Defined\n";
    }
    else
    {
      foreach ( self::$suffixes as $key => $val )
      {
        $rtn .= $key.' = '.$val."\n";
      }
    }

    $rtn .= "\n";
    $rtn .= "Defined Paths:\n";

    if ( count(self::$paths) == 0 )
    {
      $rtn .= "-- None Defined\n";
    }
    else
    {
      foreach ( self::$paths as $key => $val )
      {
        $rtn .= $key.' = '.$val."\n";
      }
    }

    $rtn .= "\n";
    $rtn .= "Defined Library Paths:\n";

    if ( count(self::$libPaths) == 0 )
    {
      $rtn .= "-- None Defined\n";
    }
    else
    {
      foreach ( self::$libPaths as $key => $val )
      {
        $rtn .= $key.' = '.$val."\n";
      }
    }

    $rtn .= "\n";
    $rtn .= "Classes Already Loaded:\n";

    if ( count(self::$loaded) == 0 )
    {
      $rtn .= "-- None Loaded Yet\n";
    }
    else
    {
      foreach ( self::$loaded as $key => $val )
      {
        $rtn .= $key.' = '.$val."\n";
      }
    }

    $rtn .= "\n";
    $rtn .= "Debug Backtrace Information\n";
    // $rtn .= print_r(self::$backTrace, true);


    // Wrap in <pre> tags if this isn't going to a command line
    if ( php_sapi_name() != 'cli' )
    {
      $rtn = '<pre>'.$rtn.'</pre>';
    }

    return $rtn;
  }
}
