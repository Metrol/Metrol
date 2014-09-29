<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol;

/**
 * Used as a conduit to Log Writers
 *
 */
class Log
{
  /**
   * Log Level Codes
   *
   */
  const DEBUG   = 0;
  const INFO    = 10;

  const ACCESS  = 40;
  const FETCH   = 50;
  const SYSTEM  = 60;
  const UPDATE  = 70;

  const NOTICE  = 256;
  const WARNING = 512;
  const ERROR   = 1024;
  const FATAL   = 2048;

  /**
   * Set of Log Writers that will be written to when asked to write out to
   * a log.
   *
   * @var \Metrol\Log\Writer\Set
   */
  protected $writerSet;

  /**
   * Sets the minimum log level that will be sent to the writers
   *
   * @var integer
   */
  protected $minLevel;

  /**
   * Singleton instance of this object
   *
   * @var \Metrol\Log
   */
  static protected $instance;

  /**
   * A generated ID for a single logging session.
   *
   * @var integer
   */
  protected $logSessionID;

  /**
   * The system registry where additional information might be found for
   * logging.
   *
   * @var \Metrol\Registry
   */
  protected $reg;

  /**
   * Instantiate this object
   *
   */
  protected function __construct()
  {
    // Create a unique log session id for this load of the logger.  Writers
    // should use this as well as a system date to identify what was being
    // logged when.
    $this->logSessionID = uniqid();

    // System registry
    $this->reg = \Metrol\Registry::init();

    // Create the data set object where the writers will be stored.
    $this->initWriterSet();

    // By default, only warnings and above are logged.
    $this->minStatus = self::WARNING;
  }

  /**
   * Provide the log session idefentifier
   *
   * @return string
   */
  public function getLogSessionID()
  {
    return $this->logSessionID;
  }

  /**
   * Write a message out to all the writers.  If there are any problems writing
   * a false will be returned.  Excution will continue.  It will be up to the
   * caller to determine how critical writing the message is.
   *
   * @param string  $source  The process that called this
   * @param string  $message What should be written
   * @param integer $status  What kind of message is this
   *
   * @throws \Metrol\Log\Exception
   */
  static public function log($source, $message, $level = self::INFO)
  {
    $log = self::getInstance();

    // Not going to even try to log if the level is below the threshold set
    if ( $level < $log->minLevel )
    {
      return;
    }

    // If no writers are configured this still doesn't qualify as a problem
    // writing to a log.
    if ( $log->writerSet->count() == 0 )
    {
      return;
    }

    $message = $log->getNewMessage()
                   ->setSource($source)
                   ->setText($message)
                   ->setLogLevel($level);

    // Walk through each of the writers to send out the message and status to
    // them.  If any are not ready, or report a problem with their ability to
    // write, then flag that as a problem.
    foreach ( $log->writerSet as $writer )
    {
      try
      {
        $writer->setMessage($message);
        $writer->save();
      }
      catch ( \Metrol\Log\Exception $e )
      {
        throw $e;
      }
    }

    return;
  }

  /**
   * Provide the one and only instance of this class
   *
   * @return \Metrol\Log
   */
  static public function getInstance()
  {
    if ( !is_object(static::$instance) )
    {
      $className = __CLASS__;
      static::$instance = new $className;
    }

    return static::$instance;
  }

  /**
   * Add a new Log Writer to the stack to be written to
   *
   * @param \Metrol\Log\Writer $writer
   */
  static public function addWriter(\Metrol\Log\Writer $writer)
  {
    $log = self::getInstance();

    $log->writerSet->addWriter($writer);
  }

  /**
   * Sets the minimum message status that will be sent to a writer.  Any value
   * below this will be ignored by all writers.
   *
   * @param integer $minStatus
   */
  static public function setMinStatus($minStatus)
  {
    $ms = intval($minStatus);

    $log = self::getInstance();

    $log->minStatus = $ms;
  }

  /**
   * Initialize the set of writers that will be used.
   *
   */
  protected function initWriterSet()
  {
    $this->writerSet = new \Metrol\Log\Writer\Set;
  }

  /**
   * Provice a new Log Message object for internal use
   *
   * @return \Metrol\Log\Message
   */
  protected function getNewMessage()
  {
    $msg = new \Metrol\Log\Message;

    $msg->setLogSessionID($this->logSessionID);

    if ( isset($this->reg->userID) )
    {
      $msg->setUserID($this->reg->userID);
    }

    return $msg;
  }
}
