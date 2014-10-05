<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Log;

/**
 * Defines the requirements that every log writer must implement
 *
 */
class Writer
{
  /**
   * Write out a log message
   *
   * @param \Metrol\Log\Message $message The log message object for the writer
   *
   * @return this
   *
   * @throws \Metrol\Log\Exception
   */
  public function setMessage(\Metrol\Log\Message $message);

  /**
   * Commit the writer to store the message to whatever it is designed to store
   * to.
   *
   * @return this
   *
   * @throws \Metrol\Log\Exception
   */
  public function save();

  /**
   * Sets a minimum status that will be accepted by this writer.  Any status
   * below this threshold should be ignored regardless of included/excluded
   * stacks.
   *
   * @param integer $minStatus
   *
   * @return this
   */
  public function setMinStatus($minStatus);

  /**
   * By default a writer should write to its output for every status.  Once this
   * has been called, only the statuses that have been added will be written
   * too.  All others will be ignored.
   *
   * @param integer $status
   *
   * @return this
   */
  public function addStatus($status);

  /**
   * Specifically exclude certain statuses, even if added with addStatusFor()
   *
   * @param integer $status
   *
   * @return this
   */
  public function excludeStatus($status);
}
