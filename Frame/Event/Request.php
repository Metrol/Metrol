<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\Event;

/**
 * The object that Event triggers use to pass information observers
 */
class Request extends \Metrol\Frame\Request
{
  /**
   * Name of the Event
   *
   * @var string
   */
  protected $eventName;

  /**
   * A simple text message that can be set for an observer to read.
   *
   * @var string
   */
  protected $message;

  /**
   * Initilizes the Event Request object
   *
   * @param string Name of the Event
   */
  public function __construct($eventName)
  {
    parent::__construct();

    $this->eventName = $eventName;
    $this->message   = 'Event Triggered';
  }

  /**
   * Extend the parent to include additional information from an Event request
   *
   * @return string
   */
  public function __toString()
  {
    $rtn = parent::__toString();

    $rtn .= "Event information:\n";
    $rtn .= "-------------------------------------------------\n";
    $rtn .= 'eventName = '.$this->eventName."\n";
    $rtn .= 'message = '.$this->message."\n";

    $rtn .= "\n\n";

    return $rtn;
  }

  /**
   * Sets a simple text message for an observer to read
   *
   * @param string
   * @return this
   */
  public function setMessage($message)
  {
    $this->message = $message;

    return $this;
  }

  /**
   * Provide whatever message may have been set
   *
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }

  /**
   * Provides the name of the Event that was triggered
   *
   * @return string
   */
  public function getEventName()
  {
    return $this->eventName;
  }
}
