<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTTP\Session;

/**
 * Defines what a Metrol session handler must implement in order to be used
 * in the Session class
 *
 */
interface Handler
{
  /**
   * Called to have this Session Handler register itself
   * 
   */
  public function register();

  /**
   * Starts up the session
   *
   * @return boolean TRUE for success, FALSE for failure.
   */
  public function open();

  /**
   * Writes data out to the session
   *
   * @param string Session Identifier
   * @param string Data to store
   */
  public function write($sessionID, $data);

  /**
   * Fetch back some data from a Session
   *
   * @param  string Session Identifier
   * @return string Data that's been asked for
   */
  public function read($sessionID);

  /**
   * End the current session and store session data.
   *
   * @return boolean TRUE for success, FALSE for failure.
   */
  public function close();

  /**
   * This callback is executed when a session is destroyed with
   * session_destroy() or with session_regenerate_id() with the destroy
   * parameter set to TRUE.
   *
   * @param  string Session Identifier
   * @return boolean TRUE for success, FALSE for failure.
   */
  public function destroy($sessionID);

  /**
   * The garbage collector callback is invoked internally by PHP periodically
   * in order to purge old session data. The frequency is controlled by
   * session.gc_probability and session.gc_divisor. The value of lifetime which
   * is passed to this callback can be set in session.gc_maxlifetime.
   *
   * @param  integer Number of seconds between garbage collection
   * @return boolean TRUE for success, FALSE for failure.
   */
  public function gc($expireSeconds);

  /**
   * Cause the session cookie to be deleted
   *
   */
  public function cookieDelete();

}
