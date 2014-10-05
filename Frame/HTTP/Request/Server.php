<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTTP\Request;

/**
 * A few of the Server variables renamed and made OO accessible.
 */
class Server
{
  /**
   * Values that will be provided
   *
   * @var array
   */
  private $vals;

  /**
   * Initilizes the Server object
   *
   * @param object
   */
  public function __construct()
  {
    $this->vals = array();

    $this->initValues();
  }

  /**
   * Diagnostic output showing the contents of this object
   *
   * @return string
   */
  public function __toString()
  {
    $rtn = 'Contents of '.get_class($this)."\n";
    $rtn .= "  ----------------------\n";

    if ( count($this->values) == 0 )
    {
      $rtn .= '  |     [Empty]'."\n";
    }

    foreach ( $this->vals as $key => $val )
    {
      $rtn .= "  | $key = ";
      $rtn .= $val."\n";
    }

    $rtn .= "  ----------------------\n";

    return $rtn;
  }


  /**
   * Initializes all the values that this object will have available
   */
  protected function initValues()
  {
    if ( isset($_SERVER['HTTP_REFERER']) )
    {
      $this->vals['referer']  = $_SERVER['HTTP_REFERER'];
      $this->vals['referrer'] = $_SERVER['HTTP_REFERER'];
    }

    if ( array_key_exists('SITE_MODE', $_SERVER) )
    {
      $this->vals['sitemode'] = $_SERVER['SITE_MODE'];
    }

    $this->vals['server']    = $_SERVER['SERVER_NAME'];
    $this->vals['host']      = $_SERVER['HTTP_HOST'];
    $this->vals['method']    = $_SERVER['REQUEST_METHOD'];
    $this->vals['docroot']   = $_SERVER['DOCUMENT_ROOT'];

    if ( array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER) )
    {
      $this->vals['language']  = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
    }
    else
    {
      $this->vals['language'] = 'en-US,en;q=0.5';
    }

    $this->vals['encoding']  = $_SERVER['HTTP_ACCEPT_ENCODING'];
    $this->vals['agent']     = $_SERVER['HTTP_USER_AGENT'];
    $this->vals['uri']       = $_SERVER['REQUEST_URI'];

    if ( array_key_exists('REDIRECT_STATUS', $_SERVER) )
    {
      $this->vals['status'] = $_SERVER['REDIRECT_STATUS'];
    }
    else
    {
      $this->vals['status'] = 200;
    }

    $this->vals['pagerequested'] = 'http://'.
                                   $_SERVER['HTTP_HOST'].
                                   $_SERVER['REQUEST_URI'];
  }

  /**
   * Provide the values we've got in here.
   *
   * @param string Key for the value
   * @return mixed
   */
  public function __get($key)
  {
    $rtn = null;

    $key = strtolower($key);

    if ( array_key_exists($key, $this->vals) )
    {
      $rtn = $this->vals[$key];
    }

    return $rtn;
  }
}
