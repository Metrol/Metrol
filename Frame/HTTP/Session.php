<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTTP;

/**
 * Provides customizeable Session handling.
 */
class Session
{
  /**
   * The cookie parameters of the session
   *
   * @var Session\Params
   */
  protected $parameters;

  /**
   * Various run time settings for the session
   *
   * @var Session\Runtime
   */
  protected $runtime;

  /**
   * If a Session handler is being used, the reference to it will be stored
   * here.
   *
   * @var Session\Handler
   */
  protected $handler;

  /**
   * Establishes the database connection and registered the methods of this
   * class to handle sessions.
   */
  public function __construct()
  {
    $this->parameters = new Session\Params;
    $this->runtime    = new Session\Runtime;
    $this->handler    = null;
  }

  /**
   * Retrieve a value from the session handler
   *
   * @param string
   * @return mixed
   */
  public function __get($key)
  {
    if ( !$this->isActive() )
    {
      return null;
    }

    if ( $key == 'id' )
    {
      return \session_id();
    }

    return $this->getVal($key);
  }

  /**
   * Set a value into the session handler
   *
   * @param string
   * @param mixed
   */
  public function __set($key, $val)
  {
    if ( !$this->isActive() )
    {
      return;
    }

    if ( $key == 'id' )
    {
      \session_id($val);

      return;
    }

    $this->setVal($key, $val);
  }

  /**
   * A diagnostic output of the session
   *
   * @return string
   */
  public function __toString()
  {
    $rtn = "<pre>";

    $rtn .= "<b>Session Details</b>\n";
    $rtn .= "Session ID: ".$this->id."\n\n";

    if ( $this->isActive() )
    {
      $rtn .= "This session has been started\n\n";
    }
    else
    {
      $rtn .= "This session has NOT been started\n\n";
    }

    $rtn .= "-- Values Stored in the Session Parameters --\n";
    $rtn .= $this->parameters->debug();

    if ( !isset($_SESSION) )
    {
      return $rtn;
    }

    $rtn .= "-- Values stored in the session --\n";

    foreach ( $_SESSION as $key => $val )
    {
      if ( is_array($val) )
      {
        $rtn .= print_r($val, true);
      }
      else
      {
        $rtn .= "$key = $val\n";
      }
    }

    $rtn .= "</pre>";

    return $rtn;
  }

  /**
   * Provide the Session Cookie Parameters object
   *
   * @return Session\Params
   */
  public function getParams()
  {
    return $this->parameters;
  }

  /**
   * Provide the Session runtime settings
   *
   * @return Session\Runtime
   */
  public function getRuntime()
  {
    return $this->runtime;
  }

  /**
   * Used to specify a custom session handler
   *
   * @param Session\Handler
   */
  public function setHandler(Session\Handler $handler)
  {
    $this->handler = $handler;
  }

  /**
   * Start up the session.
   *
   * This should be used instead of the built in session_start() function due
   * to additional logic built into this class.
   *
   * @return this
   */
  public function start()
  {
    if ( $this->isActive() )
    {
      return $this;
    }

    $this->parameters->applyToSession();

    if ( is_object($this->handler) )
    {
      $this->handler->register();
    }

    \session_start(); // Finally, get the session started already!

    return $this;
  }

  /**
   * Ends the session
   *
   * @return this
   */
  public function end()
  {
    \session_destroy();

    return $this;
  }

  /**
   * Tries to determine if this session is active or not
   *
   * @return boolean
   */
  public function isActive()
  {
    if ( isset($_SESSION) )
    {
      return true;
    }

    return false;
  }

  /**
   * Stores a key/value pair into the session.
   *
   * @param string Name of the variable being stored
   * @param mixed Value to be stored for the variable name
   */
  public function setVal($variableName, $value)
  {
    $_SESSION[$variableName] = $value;
  }

  /**
   * Retrieves a value stored in the session.
   *
   * @param string Name of the variable that had been stored
   * @return mixed The value of that variable, or null if it doesn't exist
   */
  public function getVal($variableName)
  {
    if ( array_key_exists($variableName, $_SESSION) )
    {
      return $_SESSION[$variableName];
    }
  }

  /**
   * Removes a value from the session
   *
   * @param string
   */
  public function unsetVal($variableName)
  {
    if ( array_key_exists($variableName, $_SESSION) )
    {
      unset($_SESSION[$variableName]);
    }

    return $this;
  }
}
