<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Log;

/**
 * Handle all the caught exceptions thrown by logging objects
 *
 */
class Exception extends \Metrol\Exception
{
  /**
   * Store the message sent in and pass to the Exception parent
   *
   * @param string  $message Message you want stored in this exception.
   * @param integer $errCode Exception severity
   */
  public function __construct($message = '', $errCode = self::FATAL)
  {
    parent::__construct($message, $errCode);
  }
}
