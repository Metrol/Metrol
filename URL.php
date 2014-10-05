<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol;

/**
 * Handles Uniform Resource Locators (URL) for the Metrol library
 */
class URL
{
  /**
   * The fully assembled URL
   *
   * @var string
   */
  private $urlValue;

  /**
   * The original URL passed into the setURL() method.
   *
   * @var string
   */
  private $urlOrig;

  /**
   * The transport method to be used.  Ex: http, https, ftp
   *
   * @var string
   */
  private $transportVal;

  /**
   * The domain name portion.  Ex: www.domain.com
   *
   * @var string
   */
  private $domainVal;

  /**
   * The directory of the URL
   *
   * @var string
   */
  private $dirVal;

  /**
   * File name being referenced
   *
   * @var string
   */
  private $fileVal;

  /**
   * Key/value pairs to be added to the URL
   *
   * @var array
   */
  private $passVars;

  /**
   * Port number to be added to the URL.
   *
   * @var integer
   */
  private $portVal;

  /**
   * The user name to be used in the URL
   *
   * @var string
   */
  private $userName;

  /**
   * The password to be used along with the user name
   *
   * @var string
   */
  private $passwordVal;

  /**
   * The trailing portion of the URL that specifies an anchor name on a page.
   *
   * @var string
   */
  private $anchorName;

  /**
   * Can optionally take in a URL
   * If the URL string passed in is simply "ref" then the URL will be changed
   * to the referring page.
   *
   * @param string
   */
  public function __construct($url = '')
  {
    $this->initializeUrlParts();
    $this->passVars = array();

    if ( strtolower($url) == 'ref' )
    {
      $this->setToRef();
    }
    else
    {
      $this->setURL($url);
    }
  }

  /**
   * @return string
   */
  public function __toString()
  {
    $this->assemble();

    return $this->urlValue;
  }

  /**
   * Will take apart the passed in URL and store its various components as
   * member variables.
   *
   * @param string
   * @return Metrol\URL
   */
  public function setURL($url)
  {
    if ( strlen($url) == 0 )
    {
      return $this;
    }

    $this->urlOrig = $url;

    $this->initializeUrlParts();

    $parts = parse_url($url);

    if ( array_key_exists('scheme', $parts) )
    {
      $this->transport($parts['scheme']);
    }

    if ( array_key_exists('host', $parts) )
    {
      $this->domain($parts['host']);
    }

    if ( array_key_exists('port', $parts) )
    {
      $this->port($parts['port']);
    }

    if ( array_key_exists('path', $parts) )
    {
      $path = $parts['path'];
      $path = str_replace('\\', '/', $path);

      if ( substr($path, -1) == '/' )
      {
        $this->dir($path);
      }
      else
      {
        $file = basename($path);
        $flen = strlen($file) * -1;
        $dir = substr($path, 0, $flen);
        $this->dir($dir);
        $this->file($file);
      }
    }

    if ( array_key_exists('query', $parts) )
    {
      $qp = explode('&', $parts['query']);

      foreach ($qp as $keyval)
      {
        list($key, $val) = explode('=', $keyval);
        $this->passVars[$key] = $val;
      }
    }

    if ( array_key_exists('user', $parts) )
    {
      $this->user($parts['user']);
    }

    if ( array_key_exists('pass', $parts) )
    {
      $this->pass($parts['pass']);
    }

    if ( array_key_exists('fragment', $parts) )
    {
      $this->setAnchor($parts['fragment']);
    }

    return $this;
  }

  /**
   * Resets all the stored values about the URL to be reset to a fully empty
   * state.
   */
  private function initializeUrlParts()
  {
    $this->urlValue     = '';
    $this->transportVal = '';
    $this->domainVal    = '';
    $this->dirVal       = '';
    $this->fileVal      = '';
    // $this->passVars     = array();
    $this->portVal      = 0;
    $this->userName     = '';
    $this->passwordVal  = '';
    $this->anchorName   = null;
  }

  /**
   * Takes all the various parts of the URL this object holds and tries to get
   * something reasonable squeezed out of it.
   */
  private function assemble()
  {
    $url = "";

    if ( $this->anchorName !== null )
    {
      $url .= '#'.$this->anchorName;
    }

    if ( count($this->passVars) > 0 )
    {
      $vars = '?';

      foreach ( $this->passVars as $key => $val )
      {
        $vars .= $key.'='.$val;
        $vars .= '&';
      }

      $vars = substr($vars, 0, -1);

      $url = $vars.$url;
    }

    if ( strlen($this->fileVal) > 0 )
    {
      $url = $this->fileVal.$url;
    }

    if ( strlen($this->dirVal) > 0 )
    {
      if ( substr($this->dirVal, -1) == '/' )
      {
        $url = $this->dirVal.$url;
      }
      else
      {
        $url = $this->dirVal.'/'.$url;
      }
    }

    // Put together the user:pass portion ahead of time.  Only gets added
    // if both a transport and domain exist.
    $userPass = '';

    if ( strlen($this->userName) > 0 )
    {
      $userPass = $this->userName;

      if ( strlen($this->passwordVal) > 0 )
      {
        $userPass .= ':'.$this->passwordVal;
      }

      $userPass .= '@';
    }

    if ( strlen($this->transportVal) > 0 )
    {
      if ( strlen($this->domainVal) > 0 )
      {
        $dom = $this->domainVal;

        if ( $this->portVal > 0 )
        {
          $dom .= ":".$this->portVal;
        }

        if ( strlen($this->dirVal) > 0 AND substr($this->dirVal, 0, 1) != '/' )
        {
          $dom .= '/';
        }
        elseif ( strlen($this->dirVal) == 0 )
        {
          $dom .= '/';
        }

        $url = $this->transportVal.'://'.$userPass.$dom.$url;
      }
      else
      {
        if ( $this->transportVal == 'file' )
        {
          $url = $this->transportVal.'://'.$url;
        }
        else
        {
          $url = $this->transportVal.':'.$url;
        }
      }
    }

    // With no domain name, transport, dir, or file value but we do have
    // parameters then the prefix of the URL needs to be set to "./"
    if ( !strlen($this->transportVal) OR !strlen($this->domainVal) )
    {
      if ( !strlen($this->dirVal) AND !strlen($this->fileVal) )
      {
        if ( count($this->passVars) > 0 )
        {
          $url = './'.$url;
        }
      }
    }

    $this->urlValue = $url;
  }

  /**
   * Redirects a web browser to the URL assembled by this object then exits all
   * code execution.
   *
   * @param integer
   */
  public function redirect($status = 200)
  {
    $this->assemble();

    header('Status: '.intval($status));
    header('Location: '.$this->urlValue);

    exit;
  }

  /**
   * Sets the directory path that is considered the root of the site at the
   * file system level.  Handy for file exists checks.
   *
   * @param string
   * @return string
   */
  public function osRootPath()
  {
    return $_SERVER['DOCUMENT_ROOT'];
  }

  /**
   * Combines the file system root path with the directory in the URL.
   * If a transport has been specified this returns an empty string.
   *
   * @return string
   */
  public function osPath()
  {
    $rtn = '';

    if ( strlen($this->transportVal) == 0 )
    {
      if ( substr($this->dirVal, 0, 1) == '/' )
      {
        $rtn = $this->osRootPath().$this->dirVal;
      }
      else
      {
        $rtn = $this->pathHere().'/'.$this->dirVal;
      }
    }

    return $rtn;
  }

  /**
   * Provide the directory where this script is being run from.
   *
   * @return string
   */
  public function pathHere()
  {
    $path = $_SERVER['SCRIPT_FILENAME'];

    $lastSlash = strrpos($path, '/');

    $rtn = substr($path, 0, $lastSlash);

    return $rtn;
  }

  /**
   * Provide the path from the web site root.
   */
  public function sitePath()
  {
    $rootPathLen = strlen($this->osRootPath());
    $rootPathLen = $rootPathLen;

    $sitePath = substr($this->osPath(), $rootPathLen);

    return $sitePath;
  }

  /**
   * Provides the full file system path and file name for this URL's
   * local file.
   * If a transport has been specified this returns an empty string.
   *
   * @return string
   */
  public function osFile()
  {
    $rtn = "";

    if ( strlen($this->transportVal) == 0 )
    {
      $rtn = $this->osPath()."/".$this->fileVal;
    }

    return $rtn;
  }

  /**
   * Determines if a local file actually exists on this file system.
   *
   * @return boolean
   */
  public function localFileExists()
  {
    if ( strlen($this->transportVal) > 0 )
    {
      return FALSE;
    }

    if ( file_exists($this->osFile()) )
    {
      return TRUE;
    }
    else
    {
      return FALSE;
    }
  }

  /**
   * Sets the URL to the HTTP Referrer
   *
   * @return Metrol\URL
   */
  public function setToRef()
  {
    if ( array_key_exists('HTTP_REFERER', $_SERVER) )
    {
      $ref = $_SERVER['HTTP_REFERER'];
    }
    else
    {
      $ref = '/';
    }

    if ( strlen($ref) == 0 )
    {
      $this->setURL('/');
    }
    else
    {
      $this->setURL($ref);
    }

    return $this;
  }

  /**
   * Sets the transport to use
   *
   * @param string
   * @return Metrol\URL
   */
  public function transport($transport)
  {
    $allowed = array('http', 'https', 'ftp', 'javascript', 'mailto');

    // Make sure to strip off any extra punctuation here
    if ( substr($transport, -3) == '://' )
    {
      $transport = substr($transport, 0, -3);
    }

    if ( substr($transport, -2) == ':/' )
    {
      $transport = substr($transport, 0, -2);
    }

    $transport = strtolower($transport);

    if ( !in_array($transport, $allowed) )
    {
      return;
    }

    $this->transportVal = $transport;

    return $this;
  }

  /**
   * Provide the transport type in use
   *
   * @return string
   */
  public function getTransport()
  {
    return $this->transportVal;
  }

  /**
   * Set the domain name
   *
   * @param string
   * @return Metrol\URL
   */
  public function domain($dom)
  {
    $this->domainVal = $dom;

    return $this;
  }

  /**
   * Set the TCP/IP port for the connection
   *
   * @param integer
   * @return Metrol\URL
   */
  public function port($port)
  {
    $this->portVal = intval($port);

    return $this;
  }

  /**
   * Set the directory
   *
   * @param string
   * @return Metrol\URL
   */
  public function dir($dir)
  {
    // A single dot is the same as no directory, so blank it out
    if ( $dir == '.' )
    {
      $dir = '';
    }

    // Replace any backslashes with forward ones
    $dir = str_replace('\\', '/', $dir);

    $this->dirVal = $dir;

    return $this;
  }

  /**
   * Specify the file to be pointed to.
   *
   * @param string
   * @return Metrol\URL
   */
  public function file($file)
  {
    $this->fileVal = $file;

    return $this;
  }

  /**
   * User name to be placed in the URL
   *
   * @param string
   * @return Metrol\URL
   */
  public function user($userName)
  {
    $this->userName = $userName;

    return $this;
  }

  /**
   * A plain text password that gets attached to the URL
   *
   * @param string
   * @return Metrol\URL
   */
  public function pass($password)
  {
    $this->passwordVal = $password;

    return $this;
  }

  /**
   * The anchor name to be added to the end of the URL
   *
   * @param string
   * @return Metrol\URL
   */
  public function setAnchor($anchorName)
  {
    $this->anchorName = $anchorName;

    return $this;
  }

  /**
   * Adds a key/value pair to the URL
   *
   * @param string
   * @param string
   * @return Metrol\URL
   */
  public function param($key, $value)
  {
    $this->passVars[$key] = urlencode($value);

    return $this;
  }

  /**
   * Adds all the parameters found in the $_GET variable if it hasn't already
   * been added.
   *
   * @return Metrol\URL
   */
  public function addGetParams()
  {
    foreach ( $_GET as $key => $val )
    {
      if ( !array_key_exists($key, $this->passVars) )
      {
        $this->param($key, $val);
      }
    }

    return $this;
  }

  /**
   * Provides the value of a parameter, if one exists.
   *
   * @param string Key
   * @return mixed Value
   */
  public function getParam($key)
  {
    if ( array_key_exists($key, $this->passVars) )
    {
      return $this->passVars[$key];
    }
  }

  /**
   * Removes a key/value pair from the URL based on the key
   *
   * @param string
   * @return Metrol\URL
   */
  public function delParam($key)
  {
    if ( array_key_exists($key, $this->passVars) )
    {
      unset($this->passVars[$key]);
    }

    return $this;
  }

  /**
   * Clear out all the parameters from a URL.
   *
   * @return Metrol\URL
   */
  public function delAllParam()
  {
    $this->passVars = array();
  }

  public function debug()
  {
    $rtn  = "";

    $rtn .= "<pre>\n";
    $rtn .= "<b>Original: ".$this->urlOrig."</b>\n\n";
    $rtn .= "Parsed URL: ";
    $rtn .= print_r( parse_url($this->urlOrig), true );
    $rtn .= "\n";

    $rtn .= "Root Path: ".$this->osRootPath()."\n";
    $rtn .= "Path Here: ".$this->pathHere()."\n";
    $rtn .= "OS Path  : ".$this->osPath()."\n";
    $rtn .= "OS File  : ".$this->osFile()."\n";

    if ( $this->localFileExists() )
    {
      $rtn .= "File Exists?: YES\n";
    }
    else
    {
      $rtn .= "File Exists?: NO\n";
    }

    $rtn .= "Site Path: ".$this->sitePath()."\n";
    $rtn .= "Transport: ".$this->transportVal."\n";
    $rtn .= "Domain   : ".$this->domainVal."\n";
    $rtn .= "Directory: ".$this->dirVal."\n";
    $rtn .= "File Name: ".$this->fileVal."\n";
    $rtn .= "Port     : ".$this->portVal."\n";
    $rtn .= "User Name: ".$this->userName."\n";
    $rtn .= "Password : ".$this->passwordVal."\n";
    $rtn .= "Anchor   : ".$this->anchorName."\n";

    $rtn .= "\nParameters:\n";
    $rtn .= print_r($this->passVars, true);

    $this->assemble();
    $rtn .= "\n\nResult : ".$this->urlValue;

    $rtn .= "\n\n</pre>\n";

    return $rtn;
  }
}
