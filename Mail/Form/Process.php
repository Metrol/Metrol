<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Mail\Form;

/**
 * Used to walk through all of the fields of a Metrol\Mail\Form coming through
 * on a form post and translate that information into a Metrol\Mail object or
 * into a set of objects suitable for storing the content.
 */
class Process
{
  /**
   * The form post array that is being processed
   *
   * @var array
   */
  private $post;

  /**
   * The Mail object that will be populated with the information from the form
   * post.
   *
   * @var Metrol\Mail
   */
  protected $mail;

  /**
   * Initialize the Process object
   *
   * @param \Metrol\Frame\HTTP\Request
   */
  public function __construct(\Metrol\Frame\HTTP\Request $request)
  {
    $this->post = $request->post;

    $this->mail = new \Metrol\Mail;
  }

  /**
   * Produce a diagnostic output from this class by passing the mail object
   * into it's display class.
   *
   * @return string
   */
  public function __toString()
  {
    $mailDisp = new \Metrol\Mail\Display($this->mail);

    return strval($mailDisp);
  }

  /**
   * Allow a caller to set a different mail object rather than the default
   *
   * @param \Metrol\Mail
   * @return this
   */
  public function setMailer(\Metrol\Mail $mail)
  {
    $this->mail = $mail;

    return $this;
  }

  /**
   * Get the mail object that this object has assembled.
   *
   * @return \Metrol\Mail
   */
  public function getMailer()
  {
    return $this->mail;
  }

  /**
   * Begins the process of going through the post information and populating
   * the Mail object.  Validation of the data is not performed here, though the
   * Mail object does some validation of it's own.  You need to check the Mail
   * object that what came across is valid.
   *
   * @return this
   */
  public function run()
  {
    $this->processMessageID();
    $this->processFrom();
    $this->processReplyTo();
    $this->processOrgName();
    $this->processRecipients();
    $this->processSubject();
    $this->processMessage();
    $this->processSignature();

    return $this;
  }

  /**
   * Store the message ID that may have come across into the mail object
   *
   */
  protected function processMessageID()
  {
    if ( $this->post->emailMessageID !== null )
    {
      $this->mail->setMessageID($this->post->emailMessageID);
    }
  }

  /**
   * Process the From field
   *
   */
  protected function processFrom()
  {
    $from = $this->mail->getFrom();

    $from->setFrom($this->post->emailFromEmail, $this->post->emailFromName);
  }

  /**
   * Process the Reply To field
   *
   */
  protected function processReplyTo()
  {
    $from = $this->mail->getFrom();

    $from->setReplyTo($this->post->emailReplyToEmail,
                      $this->post->emailReplyToName);
  }

  /**
   * Process the Org Name field
   *
   */
  protected function processOrgName()
  {
    $from = $this->mail->getFrom();

    if ( $this->post->emailOrgName !== null )
    {
      $from->setOrgName($this->post->emailOrgName);
    }
  }

  /**
   * Process all the recipient fields
   *
   */
  protected function processRecipients()
  {
    if ( !is_array($this->post->emailRecipientType) or
         !is_array($this->post->emailRecipientName) or
         !is_array($this->post->emailRecipientEmail) )
    {
      return;
    }

    if ( count($this->post->emailRecipientType) == 0 )
    {
      return;
    }

    $re = $this->mail->getRecipients();

    foreach ( $this->post->emailRecipientType as $idx => $type )
    {
      if ( $type == 'To' )
      {
        if ( array_key_exists($idx, $this->post->emailRecipientName) and
             array_key_exists($idx, $this->post->emailRecipientEmail) )
        {
          $re->addTo($this->post->emailRecipientEmail[$idx],
                     $this->post->emailRecipientEmail[$idx]);
        }
      }

      if ( $type == 'cc' )
      {
        if ( array_key_exists($idx, $this->post->emailRecipientName) and
             array_key_exists($idx, $this->post->emailRecipientEmail) )
        {
          $re->addCc($this->post->emailRecipientEmail[$idx],
                     $this->post->emailRecipientEmail[$idx]);
        }
      }

      if ( $type == 'bcc' )
      {
        if ( array_key_exists($idx, $this->post->emailRecipientName) and
             array_key_exists($idx, $this->post->emailRecipientEmail) )
        {
          $re->addBcc($this->post->emailRecipientEmail[$idx],
                      $this->post->emailRecipientEmail[$idx]);
        }
      }
    }

  }

  /**
   * Process the subject field
   *
   */
  protected function processSubject()
  {
    if ( $this->post->emailSubject !== null )
    {
      $this->mail->getContent()->setSubject($this->post->emailSubject);
    }
  }

  /**
   * Process the message field
   *
   */
  protected function processMessage()
  {
    if ( $this->post->emailBody !== null )
    {
      $this->mail->getContent()->set($this->post->emailBody);
    }
  }

  /**
   * Process the signature field
   *
   */
  protected function processSignature()
  {
    if ( $this->post->emailSignature !== null )
    {
      $this->mail->getFrom()->setSig($this->post->emailSignature);
    }
  }
}
