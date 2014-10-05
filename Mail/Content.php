<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Mail;

/**
 * Handles the actual content of an Email.
 */
class Content
{
  /**
   * The body of the E-Mail
   *
   * @var string
   */
  protected $body;

  /**
   * The subject of the mail.
   *
   * @var string
   */
  protected $subject;

  /**
   * Initilizes the Content object
   *
   * @param object
   */
  public function __construct()
  {
    $this->body = '';
    $this->subject = '';
    $this->htmlFlag = false;
  }

  /**
   * Provides the body of the Email back to the caller
   *
   * @return string
   */
  public function output()
  {
    return $this->body;
  }

  /**
   * Provides the output body, only wrapped to the specified character width.
   *
   * @param integer Width in characters
   * @return string
   */
  public function outputWrapped($width = 80)
  {
    $rtn = wordwrap($this->body, $width, "\n", true);

    return $rtn;
  }

  /**
   * Determines if there's enough information here for an Email to send.
   * Need to have a subject and something in the body of the mail.
   *
   * @return boolean
   */
  public function readyToSend()
  {
    $rtn = false;

    if ( strlen($this->body) > 0 and strlen($this->subject) > 0 )
    {
      $rtn = true;
    }

    return $rtn;
  }

  /**
   * Specifies what is in the body of the mail.
   *
   * @param string
   * @return this
   */
  public function set($messageText)
  {
    $this->body = $messageText;

    return $this;
  }

  /**
   * Appends text to the whatever has already been added to the body of the
   * Email.
   *
   * @param string
   * @return this
   */
  public function add($messageText)
  {
    if ( strlen($this->body) > 0 )
    {
      $this->body .= "\n";
    }

    $this->body .= $messageText;

    return $this;
  }

  /**
   * Sets the subject of the Email
   *
   * @param string
   * @return this
   */
  public function setSubject($subjectText)
  {
    $this->subject = $subjectText;

    return $this;
  }

  /**
   * Provide the subject of the Email back to the caller
   *
   * @return string
   */
  public function getSubject()
  {
    return $this->subject;
  }
}
