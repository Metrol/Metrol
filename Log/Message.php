<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Log;

/**
 * A basic message that can be passed to a Log Writer for logging
 *
 */
class Message
{
  /**
   * Log session ID
   *
   * @var string
   */
  protected $logSessionID;

  /**
   * Where did the log message come from
   *
   * @var string
   */
  protected $source;

  /**
   * The text of the message to be logged.
   *
   * @var string
   */
  protected $text;

  /**
   * Log level of the message to be logged.
   *
   * @var integer
   * @see \Metrol\Log
   */
  protected $logLevel;

  /**
   * The identification of the user when the log event was generated
   *
   * @var integer
   */
  protected $userID;

  /**
   * Instantiate the message object
   *
   */
  public function __construct()
  {
  	$this->logSessionID = null;
    $this->source       = '';
    $this->text         = '';
  	$this->logLevel     = \Metrol\Log::INFO;
  	$this->userID       = null;
  }

  /**
   * Set the logging session ID
   *
   * @param string $logSessionID Set the session ID created by the Log object
   */
  public function setLogSessionID($logSessionID)
  {
  	$this->logSessionID = $logSessionID;

  	return $this;
  }

  /**
   * Provide the logging session ID
   *
   * @return string
   */
  public function getLogSessionID()
  {
  	return $this->logSessionID;
  }

  /**
   * Sets the source where the log message came from
   *
   * @param string $source The source of the logging
   *
   * @return this
   */
  public function setSource($source)
  {
    $this->source = $source;

    return $this;
  }

  /**
   * Provide the source of the log
   *
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }

  /**
   * Sets the log message to be written
   *
   * @param string $message The text of the message
   *
   * @return this
   */
  public function setText($message)
  {
  	$this->text = $message;

  	return $this;
  }

  /**
   * Provide the message text for the log
   *
   * @return string
   */
  public function getText()
  {
  	return $this->text;
  }

  /**
   * Sets the logging level of the log event
   *
   * @param integer $logLevel Define what level of logging event
   *
   * @return this
   */
  public function setLogLevel($logLevel)
  {
  	$this->logLevel = intval($logLevel);

  	return $this;
  }

  public function getLogLevel()
  {
  	return $this->logLevel;
  }

  /**
   * Sets the user ID of who was logged in at the time of the logging event.
   *
   * @param integer $userID The user's ID
   *
   * @return this
   */
  public function setUserID($userID)
  {
  	$this->userID = intval($userID);

  	return $this;
  }

  /**
   *
   * @return integer
   */
  public function getUserID()
  {
  	return $this->userID;
  }
}
