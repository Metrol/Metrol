<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame;

/**
 * This will be the class that code triggering events will call to make that
 * happen.  It will handle all the back end of creating a request, sending it
 * to the dispatcher and all that.
 *
 */
class Event
{
  /**
   * Name of the Event that is being triggered
   *
   * @var string
   */
  protected $eventName;

  /**
   * The Event Request that will flow out to all the Event Controllers to let
   * them know what all is going on with this event.
   *
   * @var \Metrol\Frame\Event\Request
   */
  protected $request;

  /**
   * Initilizes the Event object.
   *
   * @param string Name of the Event
   */
  public function __construct($eventName)
  {
    $this->eventName = $eventName;

    $this->initRequest();
  }

  /**
   * Sets a value into the request object
   *
   * @param string
   * @param mixed
   */
  public function __set($key, $value)
  {
    $this->request->$key = $value;
  }

  /**
   * Gets a value from the request object
   *
   * @param string
   * @param mixed
   */
  public function __get($key)
  {
    return $this->request->$key;
  }

  /**
   * Is the value set?
   *
   * @param string
   * @return boolean
   */
  public function __isset($key)
  {
    return isset($this->request->$key);
  }

  /**
   * Called when everything is ready to call out to the Controllers that may
   * be listening for this Event.
   *
   */
  public function run()
  {
    $this->getDispatcher()->run();
  }

  /**
   * A quicky way to trigger an Event without the caller having to go through
   * creating the object and all that.
   *
   * @param string $eventName
   * @param string $msgText
   */
  public static function trigger($eventName, $msgText = null)
  {
    $class = get_called_class();

    $event = new $class($eventName);

    if ( $msgText !== null )
    {
      $event->setMessage($msgText);
    }

    $event->run();
  }

  /**
   * Provide the Request object being used in this Event
   *
   * @return \Metrol\Frame\Event\Request
   */
  public function getRequest()
  {
    return $this->request;
  }

  /**
   * Sends a message into the Request object
   *
   * @param string
   * @return this
   */
  public function setMessage($msgText)
  {
    $this->request->setMessage($msgText);

    return $this;
  }

  /**
   * Provide the Dispatcher to send this Event to
   *
   * @return \Metrol\Frame\Event\Dispatcher
   */
  protected function getDispatcher()
  {
    return new Event\Dispatcher($this->request);
  }

  /**
   * Initialize the Event Request object
   *
   */
  protected function initRequest()
  {
    $this->request = new Event\Request($this->eventName);
  }
}
