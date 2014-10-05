<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame;

/**
 * Defines a PHP code library to be included into an application
 */
class Library
{
  /**
   * The name of the library
   *
   * @var string
   */
  public $name;

  /**
   * The description of the library
   *
   * @var string
   */
  public $description;

  /**
   * The path to the root of the library
   *
   * @var string
   */
  public $path;

  /**
   * The class name prefix to look for with this library
   *
   * @var string
   */
  public $prefix;

  /**
   * Does the path need to be added to the PHP include search path?
   *
   * @var boolean
   */
  public $includePath;

  /**
   * Is this library enabled?
   *
   * @var boolean
   */
  public $enabled;

  /**
   * Initializes the object
   *
   * @param string Name of the library
   */
  public function __construct($libraryName)
  {
    $this->name        = $libraryName;
    $this->description = '';
    $this->path        = '';
    $this->prefix      = '';
    $this->includePath = false;
    $this->enabled     = false;
  }

  /**
   * Diagnostic output of the contents of this object
   *
   * @return string
   */
  public function __toString()
  {
    $rtn = '';

    $rtn .= "Libary Name: ".$this->name."\n";
    $rtn .= "Description: ".$this->description."\n";
    $rtn .= "Path: ".$this->path."\n";
    $rtn .= "Class Prefix: ".$this->prefix."\n";
    $rtn .= "Include in Path?: ";
    $rtn .= $this->includePath ? "Yes\n" : "No\n";
    $rtn .= "Enabled: ";
    $rtn .= $this->enabled ? "Yes\n" : "No\n";

    return $rtn;
  }
}
