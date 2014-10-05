<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol;

/**
 * Handles all the work with assembling, formatting, and sending EMails
 */
class Mail
{
  /**
   * Holds the main body and subject of the mail
   *
   * @var \Metrol\Mail\Content
   */
  protected $content;

  /**
   * Tracks all the header information that goes into a mail
   *
   * @var \Metrol\Mail\Headers
   */
  protected $headers;

  /**
   * Handles all the information about who the mail is from
   *
   * @var \Metrol\Mail\SentFrom
   */
  protected $from;

  /**
   * Keeps track of who the mail is going to be sent to
   *
   * @var \Metrol\Mail\Recipients
   */
  protected $recipients;

  /**
   * A flag that allows control of whether or not the send() method actually
   * sends the email, or just pretends to.
   *
   * @var boolean
   */
  protected $clearToSend;

  /**
   * Maintains an option identifier for an Email.  Mostly this is meant for
   * editing an existing Email to be used by a client object as it sees fit.
   *
   * @var integer
   */
  protected $messageID;

  /**
   * Initializes the Mail objects
   */
  public function __construct()
  {
    $this->content    = new Mail\Content;
    $this->headers    = new Mail\Headers;
    $this->from       = new Mail\SentFrom;
    $this->recipients = new Mail\Recipients;

    $this->messageID   = 0;
    $this->clearToSend = false; // Default to simulate sending

    $this->headers->setMailerApp("Metrol Mail");
  }

  /**
   * Sets whether or not the send() method will actually send an Email or just
   * simulate it.
   *
   * @param boolean
   * @return Mail
   */
  public function setEnableSend($flag = true)
  {
    if ( $flag )
    {
      $this->clearToSend = true;
    }
    else
    {
      $this->clearToSend = false;
    }

    return $this;
  }

  /**
   * Provide the Content object
   *
   * @return \Metrol\Mail\Content
   */
  public function getContent()
  {
    return $this->content;
  }

  /**
   * Provide the Headers object
   *
   * @return \Metrol\Mail\Headers
   */
  public function getHeaders()
  {
    return $this->headers;
  }

  /**
   * Provide the From object
   *
   * @return \Metrol\Mail\SentFrom
   */
  public function getFrom()
  {
    return $this->from;
  }

  /**
   * Provide the Recipients Object
   *
   * @return \Metrol\Mail\Recipients
   */
  public function getRecipients()
  {
    return $this->recipients;
  }

  /**
   * Sends out the mail based on the data collected
   *
   * @return boolean If the mail was sent successfully
   */
  public function send()
  {
    if ( !$this->clearToSend )
    {
      return true;
    }

    if ( !$this->readyToSend() )
    {
      return false;
    }

    $mailTo  = $this->recipients->getMailTo();
    $subject = $this->content->getSubject();
    $body    = $this->content->output();

    $this->recipients->populateHeaders($this->headers);
    $this->from->populateHeaders($this->headers);

    $sigText = $this->from->getSignature();

    if ( strlen($sigText) > 0 )
    {
      $body .= "\n\n-- \n";
      $body .= $sigText;
    }

    $headers = $this->headers->output();

    $rtn = \mail($mailTo, $subject, $body, $headers);

    return $rtn;
  }

  /**
   * Determines if there's enough information gathered to actually send out an
   * Email.
   *
   * @return boolean
   */
  protected function readyToSend()
  {
    $ready = true;

    if ( !$this->from->readyToSend() )
    {
      $ready = false;
    }

    if ( !$this->recipients->readyToSend() )
    {
      $ready = false;
    }

    if ( !$this->content->readyToSend() )
    {
      $ready = false;
    }

    return $ready;
  }

  /**
   * Sets the message ID variable in this mail
   *
   * @param integer
   * @return this
   */
  public function setMessageID($id)
  {
    $this->messageID = intval($id);
  }

  /**
   * Gets the message id that has been set, or the default value of 0 of it
   * hasn't.
   *
   * @return integer
   */
  public function getMessageID()
  {
    return $this->messageID;
  }
}
