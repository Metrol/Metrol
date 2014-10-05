<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Mail;
use Metrol\HTML\Form as frm;

/**
 * Handles assembling form objects, putting those objects into a table, and
 * processing of an Email form
 */
class Form
{
  /**
   * The Mail object being used
   *
   * @var \Metrol\Mail
   */
  protected $mail;

  /**
   * The form object that fields will be going into
   *
   * @var \Metrol\HTML\Form
   */
  protected $form;

  /**
   * Initilizes the Form object
   *
   * @param \Metrol\Mail
   */
  public function __construct(\Metrol\Mail $mail)
  {
    $this->mail = $mail;

    $this->form = new \Metrol\HTML\Form;
    $this->initFormObjects();
  }

  /**
   * This needs to be called if the contents of the Mail object have changed.
   * Otherwise, the changes to the Mail will not be reflected on the form.
   *
   * @return this
   */
  public function reApplyMail()
  {
    $this->form = new \Metrol\HTML\Form;
    $this->initFormObjects();

    return $this;
  }

  /**
   * Produces all of the Form objects needed for displaying a form in either a
   * Table or Template.
   *
   * @return \Metrol\HTML\Form
   */
  public function getForm()
  {
    return $this->form;
  }

  /**
   * Provides the Mail object that was passed into this object
   *
   * @return \Metrol\Mail
   */
  public function getMail()
  {
    return $this->mail;
  }

  /**
   * Used to set the form objects from the mail in place
   *
   */
  protected function initFormObjects()
  {
    $this->form->open()->setName('emailSend');

    $this->fromFormObjects();
    $this->recipientFormObjects();
    $this->contentFormObjects();
    $this->jsButtonFormObjects();
  }

  /**
   * Get the From and Reply-To fields together
   *
   */
  protected function fromFormObjects()
  {
    $from = $this->mail->getFrom();

    $this->form->createField('emailFromName')
      ->setTag( new frm\Input\Text )
      ->setLabelText('From')
      ->getTag()
        ->setMax(40)->setSize(20)
        ->setPlaceholder('Name who the mail is from')
        ->setTitle('Put the name of the sender here')
        ->setValue($from->getFromName());

    $this->form->createField('emailFromEmail')
      ->setTag( new frm\Input\Email )
      ->setLabelText('From')
      ->getTag()
        ->setMax(120)->setSize(40)
        ->setPlaceholder('Email address this mail is from')
        ->setTitle('Put the Email address as to where this mail is from here')
        ->setValue($from->getFromEmail());

    $this->form->createField('emailReplyToName')
      ->setTag( new frm\Input\Text )
      ->setLabelText('Reply To')
      ->getTag()
        ->setMax(40)->setSize(20)
        ->setPlaceholder('Reply to name')
        ->setTitle('The name of the person a reply should go to')
        ->setValue($from->getReplyToName());

    $this->form->createField('emailReplyToEmail')
      ->setTag( new frm\Input\Email )
      ->setLabelText('Reply To')
      ->getTag()
        ->setMax(120)->setSize(40)
        ->setPlaceholder('Email address replies should go to')
        ->setTitle('Put the Email address replies should go to')
        ->setValue($from->getReplyToEmail());

    $this->form->createField('emailOrgName')
      ->setTag( new frm\Input\Text )
      ->setLabelText('Organization')
      ->getTag()
        ->setMax(120)->setSize(40)
        ->setPlaceholder('Name of the organization this is from')
        ->setTitle('Name of the organization this is from')
        ->setValue($from->getOrgName());

    $this->form->createField('emailSignature')
      ->setTag( new frm\TextArea )
      ->setLabelText('Signature Area')
      ->getTag()
         ->setPlaceHolder('Enter the signature area of your Email here')
         ->setTitle('The signature area of your Email')
         ->setValue( $from->getSignature() )
         ->setColumnWidth(76)->setRows(4);
  }

  /**
   * Get together the form objects for the recipients
   *
   */
  protected function recipientFormObjects()
  {
    $recipients = $this->mail->getRecipients()->getAllRecipients();

    $types = array('To' => 'To', 'cc' => 'cc', 'bcc' => 'bcc');

    $typeDrop = new frm\Select;
    $typeDrop->addArray($types);
    $typeDrop->setTitle('Use this to select what type of recipient this is');

    $recipName = new frm\Input\Text;
    $recipName->setMax(40)->setSize(20)
              ->setPlaceholder('The name of the person')
              ->setTitle('Put the name of the recipient here');

    $recipEmail = new frm\Input\Email;
    $recipEmail->setMax(120)->setSize(40)
              ->setPlaceholder('Email address')
              ->setTitle('Put the Email address of the recipient here');

    $this->form->createField('emailRecipientType')
      ->setTag($typeDrop);

    $this->form->createField('emailRecipientName')
      ->setTag($recipName);

    $this->form->createField('emailRecipientEmail')
      ->setTag($recipEmail);

    foreach ( $recipients as $idx => $recip )
    {
      $this->form->emailRecipientType->getIndex($idx)->setValue($recip->type);
      $this->form->emailRecipientName->getIndex($idx)->setValue($recip->name);
      $this->form->emailRecipientEmail->getIndex($idx)->setValue($recip->email);
    }
  }

  /**
   * Gets together the content form objects for the mail form
   *
   */
  protected function contentFormObjects()
  {
    $this->form->createField('emailSubject')
      ->setTag( new frm\Input\Text )
      ->setLabelText('Subject')
      ->getTag()
        ->setPlaceHolder('Subject for this Email')
        ->setTitle('Enter the subject for your Email here please')
        ->setValue($this->mail->getContent()->getSubject())
        ->setMax(90)->setSize(60);

    $this->form->createField('emailBody')
      ->setTag( new frm\TextArea )
      ->setLabelText('Message Body')
      ->getTag()
         ->setPlaceHolder('Type the message for your Email here')
         ->setTitle('Email message to send')
         ->setValue($this->mail->getContent()->output())
         ->setColumnWidth(76)->setRows(10);

    $this->form->createField('emailMessageID')
      ->setTag( new frm\Input\Hidden )
      ->getTag()
        ->setValue($this->mail->getMessageID());
  }

  /**
   * Adds some extra JS buttons that can be used on an Email form to add/remove
   * recipients and send the message.
   *
   */
  protected function jsButtonFormObjects()
  {
    $this->form->createField('emailAddRecipient')
      ->setTag( new frm\Input\Button )
      ->getTag()
        ->setValue('+')
        ->setTitle('Click here to add a new recipient to this Email')
        ->setOnClick('addRecipientRow()');

    $this->form->createField('emailDeleteRecipient')
      ->setTag( new frm\Input\Button )
      ->getTag()
        ->setValue('[X]')
        ->setTitle('Clicking here will remove this recipient from the Email')
        ->setOnClick('deleteRecip(this)');

    $this->form->createField('emailSend')
      ->setTag( new frm\Input\Button )
      ->getTag()
        ->setValue('Send Now')
        ->setTitle('Click here to send this mail')
        ->setOnClick('emailSend()');

    $this->form->createField('emailSubmit')
      ->setTag( new frm\Input\Submit )
      ->getTag()
        ->setTitle('Click here to submit this mail')
        ->setValue('Submit Mail');
  }
}
