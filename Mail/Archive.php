<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Mail;

/**
 * A class that provides some basic features for saving/loading an Email to a
 * database.
 */
abstract class Archive
{
  /**
   * The Mail object that needs to be saved
   *
   * @var \Metrol\Mail
   */
  protected $mail;

  /**
   * The Record object for saving the content of the Email
   *
   * @var \Metrol\Db\Item\Record
   */
  protected $mailDbObj;

  /**
   * Cross reference of field names used in the Mail DB object.
   *
   * @var \stdClass
   */
  protected $mailFields;

  /**
   * The Db Set object that will hold the recipients
   *
   * @var \Metrol\Db\Item\Record\Set
   */
  protected $recipSetObj;

  /**
   * Cross reference of field names used in the Recipient DB object.
   *
   * @var \stdClass
   */
  protected $recipFields;

  /**
   * Initialize the Save object
   *
   * @param \Metrol\Mail
   */
  public function __construct(\Metrol\Mail $mail)
  {
    $this->mail = $mail;

    $this->mailDbObj   = null;
    $this->recipSetObj = null;
    $this->mailFields  = new \stdClass;
    $this->recipFields = new \stdClass;

    $this->initFields();
  }

  /**
   * Assumes there is a good Mail and Recipient record to work with, which will
   * be used to write out the mail record.
   *
   */
  public function save()
  {
    if ( $this->mailDbObj === null or $this->recipSetObj === null )
    {
      return;
    }

    $this->populateMailObj();
    $this->populateRecipientSet();
  }

  /**
   * Takes the content of the Mail object and writes it out to the Mail DB
   * record.
   *
   */
  protected function populateMailObj()
  {
    $o  = $this->mailDbObj;
    $mf = $this->mailFields;
    $m  = $this->mail;

    $content = $m->getContent();
    $from    = $m->getFrom();
    $headers = $m->getHeaders();

    $o->setValue($mf->body, $content->output());
    $o->setValue($mf->subject, $content->getSubject());

    $o->setValue($mf->fromName,    $from->getFromName());
    $o->setValue($mf->fromAddr,    $from->getFromEmail());
    $o->setValue($mf->fromOrg,     $from->getOrgName());
    $o->setValue($mf->replyToName, $from->getReplyToName());
    $o->setValue($mf->replyToAddr, $from->getReplyToEmail());
    $o->setValue($mf->signature,   $from->getSignature());

    $o->setValue($mf->html,        $headers->isContentHTML());

    $o->save();
  }

  /**
   * Populates the Recipients data set and attaches each of the records to the
   * sent mail object.
   *
   */
  protected function populateRecipientSet()
  {
    $mailSent    = $this->mailDbObj;
    $mailSentKey = $this->mailDbObj->getSource()->primaryKey;
    $rf          = $this->recipFields;
    $recipSet    = $this->recipSetObj;

    $recipients = $this->mail->getRecipients()->getAllRecipients();

    foreach ( $recipients as $recipient )
    {
      $recipItem = $recipSet->emptyItem();
      $recipItem->$mailSentKey = $mailSent->id;
      $recipItem->setValue($rf->type, $recipient->type);
      $recipItem->setValue($rf->name, $recipient->name);
      $recipItem->setValue($rf->addr, $recipient->email);

      $recipItem->save();
      $recipSet->add($recipItem);
    }
  }

  /**
   * Set the Mail Db Record object that will be saving the Email.
   *
   * @param \Metrol\Db\Item\Record
   * @return this
   */
  public function setMailRecord(\Metrol\Db\Item\Record $mailDbObj)
  {
    $this->mailDbObj = $mailDbObj;

    return $this;
  }

  /**
   * Set the Recipient Db Record object that will be saving the Recipients.
   *
   * @param \Metrol\Db\Item\Record
   * @return this
   */
  public function setRecipientSet(\Metrol\Db\Item\Record\Set $recipSet)
  {
    $this->recipSetObj = $recipSet;

    return $this;
  }

  /**
   * Maps generic names used by this object to the actual field names used in
   * the database.  This is to allow a child class to override names as needed.
   *
   */
  protected function initFields()
  {
    $this->mailFields->sentOn      = 'sentOn';
    $this->mailFields->html        = 'html';
    $this->mailFields->fromName    = 'fromName';
    $this->mailFields->fromAddr    = 'fromAddr';
    $this->mailFields->fromOrg     = 'fromOrg';
    $this->mailFields->replyToName = 'replyToName';
    $this->mailFields->replyToAddr = 'replyToAddr';
    $this->mailFields->subject     = 'subject';
    $this->mailFields->body        = 'body';
    $this->mailFields->signature   = 'signature';

    $this->recipFields->name = 'name';
    $this->recipFields->addr = 'addr';
    $this->recipFields->type = 'type';
  }
}
