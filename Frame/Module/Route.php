<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Module;

/**
 * This Module route will contain all the basic information that Module needs
 * to get up and running into the larger framework.
 *
 */
class Route extends \Metrol\Frame\Route
{
  /**
   * The file path to the module's root directory.
   *
   * @var string
   */
  protected $root;

  /**
   * The file path to the module's source directory.
   *
   * @var string
   */
  protected $source;

  /**
   * File path to the configuration directory of this module
   *
   * @var string
   */
  protected $config;

  /**
   * Quick description of the module.  Mostly for use in diagnostic output of
   * what all is loaded
   *
   * @var string
   */
  protected $description;

  /**
   * Is this module enabled?
   *
   * @var boolean
   */
  protected $enabled;

  /**
   * Has this module been loaded yet?
   *
   * @var boolean
   */
  protected $loaded;

  /**
   * Initilizes the Route object
   *
   * @param string Name of the route. Same as the name of the Module.
   */
  public function __construct($routeName)
  {
    parent::__construct($routeName);

    $this->description = $routeName.' Module';
    $this->enabled = true;
    $this->loaded  = false;

    if ( defined('APP_PATH') )
    {
      $this->root = APP_PATH.'/module/'.$routeName;
      $this->source = $this->root.'/src';
      $this->config = $this->root.'/etc';
    }
  }

  /**
   * Extend the parent method to include Module specific information
   *
   * @return string
   */
  public function __toString()
  {
    $rtn = parent::__toString();

    $rtn .= ' Module Desc: '.$this->description."\n";
    $rtn .= ' Module Root: '.$this->root."\n";
    $rtn .= ' Source Path: '.$this->source."\n";
    $rtn .= ' Config Path: '.$this->config."\n";
    $rtn .= '     Enabled: ';
    $rtn .= $this->enabled ? "True\n" : "False\n";
    $rtn .= '      Loaded: ';
    $rtn .= $this->loaded ? "True\n" : "False\n";

    return $rtn;
  }

  /**
   * Sets the description of this Module
   *
   * @param string $description What all the module is about
   *
   * @return this
   */
  public function setDescription($description)
  {
    $this->description = $description;

    return $this;
  }

  /**
   * Provide the description of this module
   *
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }

  /**
   * Sets the root directory of this Module
   *
   * @param string $dir The top most directory of the module
   *
   * @return this
   */
  public function setRoot($dir)
  {
    $this->root = $dir.'/'.$this->name;

    $this->setSource('src');
    $this->setConfig('etc');

    return $this;
  }

  /**
   * Provide the root directory of this Module
   *
   * @return string
   */
  public function getRoot()
  {
    return $this->root;
  }

  /**
   * Sets the source directory of this Module
   * If no prefix of "/" the default is to be a sub directory of the Module's
   * root directory.
   *
   * @param string $dir Path to where the PHP source code is located
   *
   * @return this
   */
  public function setSource($dir)
  {
    if ( substr($dir, 0, 1) == '/' )
    {
      $this->source = $dir;
    }
    else
    {
      $this->source = $this->root.'/'.$dir;
    }

    return $this;
  }

  /**
   * Provide the source directory of this Module
   *
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }

  /**
   * Sets the configuration directory of this Module
   * If no prefix of "/" the default is to be a sub directory of the Module's
   * root directory.
   *
   * @param string $dir Path to where the configuration files are found
   *
   * @return this
   */
  public function setConfig($dir)
  {
    if ( substr($dir, 0, 1) == '/' )
    {
      $this->config = $dir;
    }
    else
    {
      $this->config = $this->root.'/'.$dir;
    }

    return $this;
  }

  /**
   * Provide the configuration directory of this Module
   *
   * @return string
   */
  public function getConfig()
  {
    return $this->config;
  }

  /**
   * Sets if this Module is enabled
   *
   * @param string $flag
   *
   * @return this
   */
  public function setEnabled($flag)
  {
    $flag = strtolower($flag);

    if ( $flag == 'true' or $flag == 't' or $flag == '1' )
    {
      $this->enabled = true;
    }

    if ( $flag == '' or $flag == 'false' or $flag == 'f' or $flag == '0' )
    {
      $this->enabled = false;
    }

    return $this;
  }

  /**
   * Is this module enabled?
   *
   * @return boolean
   */
  public function isEnabled()
  {
    return $this->enabled;
  }

  /**
   * Sets the flag to determine if this Module is loaded or not
   *
   * @param boolean $flag
   *
   * @return this
   */
  public function setLoaded($flag)
  {
    if ( $flag )
    {
      $this->loaded = true;
    }
    else
    {
      $this->loaded = false;
    }

    return $this;
  }

  /**
   * Is this module loaded?
   *
   * @return boolean
   */
  public function isLoaded()
  {
    return $this->loaded;
  }
}
