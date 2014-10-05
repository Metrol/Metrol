<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame;

/**
 * Description of a Request coming from some client application
 */
class Request
{
  /**
   * List of keyed values that make up the request
   *
   * @var array
   */
  protected $keyVals;

  /**
   * Initiates the Request
   */
  public function __construct()
  {
    $this->keyVals = array();
  }

  /**
   * Provides a diagnostic output of all the defined variables
   *
   * @return string
   */
  public function __toString()
  {
    $rtn = '';

    $rtn .= '** The Request Values from '.get_class($this)."\n";
    $rtn .= '================================================='."\n\n";

    $rtn .= "User Defined Values:\n";
    $rtn .= '-------------------------------------------------'."\n";


    foreach ( $this->keyVals as $key => $value )
    {
      $rtn .= $key.' = ';
      $rtn .= $value;
      $rtn .= "\n";
    }

    $rtn .= "\n";

    return $rtn;
  }

  /**
   * @param string Key value being requested
   */
  public function __get($key)
  {
    $rtn = null;

    if ( array_key_exists($key, $this->keyVals) )
    {
      $rtn = $this->keyVals[$key];
    }

    return $rtn;
  }

  /**
   * @param string Key name to set
   * @param mixed
   */
  public function __set($key, $value)
  {
    $this->keyVals[$key] = $value;
  }

  /**
   * Does the value exist
   *
   * @param string
   * @return boolean
   */
  public function __isset($key)
  {
    if ( array_key_exists($key, $this->keyVals) )
    {
      return true;
    }

    return false;
  }
}
