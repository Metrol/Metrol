<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Mail;

/**
 * Holds various kinds of information about who is sending the Email.
 */
class SentFrom
{
  /**
   * The name who the mail is from
   *
   * @var string
   */
  private $fromName;

  /**
   * The Email address the mail is coming from
   *
   * @var string
   */
  private $fromEmail;

  /**
   * The Email address the recipient should reply to if different from the
   * sender's.
   *
   * @var string
   */
  private $replyTo;

  /**
   * By default the fromName is used for a reply to address.  If this member is
   * given a value that overrides that default and uses this name.
   *
   * @var string
   */
  private $replyToName;

  /**
   * Name of the organization the mail is coming from.
   *
   * @var string
   */
  private $organization;

  /**
   * The sender's body signature that should be appended to the body of an
   * Email message.
   *
   * @var string
   */
  private $mailBodySignature;

  /**
   * The Email validation object to be used on every address passed into here.
   *
   * @var Validate
   */
  private $eVal;

  /**
   * Initilizes the From object
   *
   * @param object
   */
  public function __construct()
  {
    $this->fromName          = '';
    $this->fromEmail         = '';
    $this->replyTo           = '';
    $this->replyToName       = '';
    $this->organization      = '';
    $this->mailBodySignature = '';

    $this->eVal = new Validate();
  }

  /**
   * Determines if there's enough information here for an Email to send
   *
   * @return boolean
   */
  public function readyToSend()
  {
    $rtn = false;

    if ( strlen($this->fromEmail) > 0 )
    {
      $rtn = true;
    }

    return $rtn;
  }

  /**
   * Sets who the Email should be from
   *
   * @param string
   * @param string
   * @return this
   */
  public function setFrom($email, $name = '')
  {
    $email = substr($email, 0, 255);
    $name  = substr($name, 0, 255);

    if ( $this->eVal->setEmail($email)->validate() )
    {
      $this->fromName  = strval($name);
      $this->fromEmail = $email;
    }

    return $this;
  }

  /**
   * Sets an optional reply-to address
   *
   * @param string
   * @param string Defaults to the name set in setFrom() if left blank
   * @return this
   */
  public function setReplyTo($email, $name = '')
  {
    $email = substr($email, 0, 255);

    if ( $this->eVal->setEmail($email)->validate() )
    {
      $this->replyTo     = $email;
      $this->replyToName = $name;
    }

    return $this;
  }

  /**
   * Sets the organization name that appears in the headers of the mail
   *
   * @param string
   * @return this
   */
  public function setOrgName($orgName)
  {
    $this->organization = substr($orgName, 0, 255);

    return $this;
  }

  /**
   * Sets the signature that appears about the person the mail is from just
   * below the body of the mail.
   *
   * @param string
   * @return this
   */
  public function setSig($sigText)
  {
    $this->mailBodySignature = trim($sigText);
  }

  /**
   * Appends to the body signature already defined.
   *
   * @param string
   * @return this
   */
  public function addToSig($sigText)
  {
    if ( strlen($this->mailBodySignature) > 0 )
    {
      $this->mailBodySignature .= "\n";
    }

    $this->mailBodySignature .= trim($sigText);
  }

  /**
   * Provide the From line formatted and ready to go into an Email header
   *
   * @return string
   */
  public function getFrom()
  {
    $rtn = '';

    if ( strlen($this->fromEmail) > 0 )
    {
      if ( strlen($this->fromName) > 0 )
      {
        $rtn .= '"'.$this->fromName.'" ';
      }

      $rtn .= '<'.$this->fromEmail.'>';
    }

    return $rtn;
  }

  /**
   * Provide just the name of the person the mail is from.
   *
   * @return string
   */
  public function getFromName()
  {
    return $this->fromName;
  }

  /**
   * Provide the Email address of the person the mail is from.
   *
   * @return string
   */
  public function getFromEmail()
  {
    return $this->fromEmail;
  }

  /**
   * Provides the Reply-To line formatted for an Email header
   *
   * @return string
   */
  public function getReplyTo()
  {
    $rtn = '';

    if ( strlen($this->replyTo) > 0 )
    {
      if ( strlen($this->replyToName) > 0 )
      {
        $rtn .= '"'.$this->replyToName.'"';
      }
      else if ( strlen($this->fromName) > 0 )
      {
        $rtn .= '"'.$this->fromName.'" ';
      }

      $rtn .= '<'.$this->replyTo.'>';
    }

    return $rtn;
  }

  /**
   * Provide the reply to name
   *
   * @return string
   */
  public function getReplyToName()
  {
    return $this->replyToName;
  }

  /**
   * Provide the reply to address only
   *
   * @return string
   */
  public function getReplyToEmail()
  {
    return $this->replyTo;
  }

  /**
   * Provide the organization name that was set here
   *
   * @return string
   */
  public function getOrgName()
  {
    return $this->organization;
  }

  /**
   * Adds information to the Headers object passed in
   *
   * @param \Metrol\Mail\Headers
   */
  public function populateHeaders(Headers $headers)
  {
    // Clear any previous values that may be in there
    $headers->delete('From');
    $headers->delete('Reply-To');
    $headers->delete('Organization');

    $from = $this->getFrom();

    if ( strlen($from) > 0 )
    {
      $headers->setFrom($from);
    }

    $replyTo = $this->getReplyTo();

    if ( strlen($replyTo) > 0 )
    {
      $headers->setReplyTo($replyTo);
    }

    if ( strlen($this->organization) > 0 )
    {
      $headers->setOrganization($this->organization);
    }
  }

  /**
   * Provide the signature text
   *
   * @return string
   */
  public function getSignature()
  {
    return $this->mailBodySignature;
  }
}
