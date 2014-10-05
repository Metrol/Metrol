<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTTP\Session;

/**
 * Describe the parameters for the session
 */
class Params
{
  /**
   * The name of the session
   *
   * @var string
   */
  private $sessionName;

  /**
   * How long the session is good for in seconds
   *
   * @var integer
   */
  private $life;

  /**
   * Path on the domain where the cookie will work.
   *
   * @var string
   */
  private $path;

  /**
   * Cookie domain, for example 'www.php.net'
   *
   * @var string
   */
  private $domain;

  /**
   * If TRUE cookie will only be sent over secure connections
   *
   * @var boolean
   */
  private $secure;

  /**
   * If set to TRUE then PHP will attempt to send the httponly flag when
   * setting the session cookie.
   *
   * @var boolean
   */
  private $httpOnly;

  /**
   * Initialize the Params object
   */
  public function __construct()
  {
    $this->sessionName       = 'MetSessID';
    $this->life              = 0;
    $this->path              = '/';
    $this->domain            = '';
    $this->secure            = false;
    $this->httpOnly          = true;
  }

  /**
   * Produces an a diagnostic output of the values this class provides
   *
   * @return string
   */
  public function debug()
  {
    $rtn = '';

    $rtn .= '** Session Parameter Values from '.get_class($this)."\n";
    $rtn .= '-------------------------------------------------'."\n";

    $rtn .= 'sessionName = '.$this->sessionName."\n";
    $rtn .= 'life        = '.$this->life."\n";
    $rtn .= 'path        = '.$this->path."\n";
    $rtn .= 'domain      = '.$this->domain."\n";

    $rtn .= 'secure      = ';

    if ( $this->secure )
    {
      $rtn .= "TRUE\n";
    }
    else
    {
      $rtn .= "FALSE\n";
    }

    $rtn .= 'httpOnly    = ';

    if ( $this->httpOnly )
    {
      $rtn .= "TRUE\n";
    }
    else
    {
      $rtn .= "FALSE\n";
    }

    return $rtn;
  }

  /**
   * Takes all the settings that have been applied to the parameters here and
   * push them into the session.
   */
  public function applyToSession()
  {
    if ( strlen($this->sessionName) > 0 )
    {
      \session_name($this->sessionName);
    }

    \session_set_cookie_params($this->life, $this->path, $this->domain,
                              $this->secure, $this->httpOnly);
  }

  /**
   * Called to delete the cookie that the session has been utilizing
   *
   * @todo This doesn't do anything just yet.
   */
  public function deleteCookie()
  {
    $hourAgo = \time() - 3600;
  }

  /**
   * If TRUE cookie will only be sent over secure connections.
   *
   * @param boolean
   * @return this
   */
  public function setSecure($useSSL)
  {
    if ( $useSSL )
    {
      $this->secure = true;
    }
    else
    {
      $this->secure = false;
    }

    return $this;
  }

  /**
   * Sets up the name of the session.
   *
   * @param string
   * @return this
   */
  public function setSessionName($sessionName)
  {
    $this->sessionName = $sessionName;

    return $this;
  }

  /**
   * Provides the currect session name in active use.
   * If the session hasn't been started yet, this returns NULL
   *
   * @return string
   */
  public function getSessionName()
  {
    return \session_name();
  }

  /**
   * Path on the domain where the cookie will work. Use a single slash
   * ('/') for all paths on the domain.
   *
   * @var string
   * @return \Metrol\HTTP\Session\Params
   */
  public function setPath($path)
  {
    $this->path = $path;

    return $this;
  }

  /**
   * The path on the domain
   *
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }

  /**
   * Sets the number of seconds an inactive session will remain open.
   * Must be called before the start() method or this does nothing.
   *
   * @param integer
   * @return \Metrol\HTTP\Session\Params
   */
  public function setLifetime($seconds)
  {
    $this->life = (int) $seconds;

    return $this;
  }

  /**
   * Used to set the domain name this cookie is valid for.
   *
   * @param string
   * @return \Metrol\HTTP\Session\Params
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;

    return $this;
  }

  /**
   * Get the domain being used back out again
   *
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }

  /**
   * Used to specify whether or not to only allow the session cookie to come
   * across via HTTP only.
   *
   * @param boolean
   * @return \Metrol\HTTP\Session\Params
   */
  public function setHttpOnly($flag)
  {
    if ( $flag )
    {
      $this->httpOnly = true;
    }
    else
    {
      $this->httpOnly = false;
    }

    return $this;
  }
}
