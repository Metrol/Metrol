<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol;

use Metrol\Text;

/**
 * Handle all the caught exceptions thrown by Metrol classes
 *
 */
class Exception extends \Exception
{
  /**
   * Severity codes
   *
   */
  const NOTICE  = 256;
  const WARNING = 512;
  const ERROR   = 1024;
  const FATAL   = 2048;

  /**
   * Store the message sent in and pass to the Exception parent
   *
   * @param string Message you want stored in this exception.
   */
  public function __construct($message = '', $errCode = self::NOTICE)
  {
    parent::__construct($message, $errCode);
  }

  /**
   * Quick dump of the message.
   *
   * @return string
   */
  public function __toString()
  {
    return $this->message;
  }

  /**
   * Set the message that is attached to the exception.  This will replace
   * whatever is already in the message.
   *
   * @param string $message What information will be attached to the exception
   *
   * @return \Metrol\Exception
   */
  public function setMessage($message)
  {
    $this->message = $message;

    return $this;
  }

  /**
   * Allows the try block to tack on more information to the message
   * generated from the throw.
   *
   * @param string $message More text to add to the message
   *
   * @return \Metrol\Exception
   */
  public function addToMsg($message)
  {
    $this->message .= "\n";
    $this->message .= $message;

    return $this;
  }
}
