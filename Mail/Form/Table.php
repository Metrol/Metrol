<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Mail\Form;

/**
 * Displays an Email form into a set of tables.
 */
class Table
{
  /**
   * The mail form object passed into here
   *
   * @var \Metrol\Mail\Form
   */
  protected $mailForm;

  /**
   * Initialize the Table object
   *
   * @param \Metrol\Mail\Form
   */
  public function __construct(\Metrol\Mail\Form $mailForm)
  {
    $this->mailForm = $mailForm;
  }

  /**
   * Provides back the HTML form that was initially passed into this object
   *
   * @return \Metrol\Mail\Form
   */
  public function getMailForm()
  {
    return $this->mailForm;
  }

  /**
   * Produces one possible Email form placed into a Table object.  Meant as an
   * example only.
   *
   * @return \Metrol\HTML\Table
   */
  public function getTable()
  {
    $form = $this->mailForm->getForm();

    $t = new \Metrol\HTML\Table('Email Form');
    $t->setCaption('Example EMail Form');
    $t->setBorder(1);

    $t->setBefore($form->open());
    $t->setAfter( $form->close());

    $t->addRow( $this->getFromTable() );
    $t->addRow( $this->getReplyToTable() );
    $t->addRow( $this->getOrgNameTable() );
    $t->addRow( $this->getRecipientTable() );
    $t->addRow( $this->getSubjectTable() );
    $t->addRow( $this->getMessageTable() );
    $t->addRow( $this->getSignatureTable() );
    $t->addRow( $this->getSubmitButtonTable() );

    return $t;
  }

  /**
   * Produce the from area of the mail from
   *
   * @return \Metrol\HTML\Table
   */
  public function getFromTable()
  {
    $form = $this->mailForm->getForm();

    $t = new \Metrol\HTML\Table('From');

    $t->addRow(  $form->emailFromName->getLabel() );
    $t->addCell( $form->emailFromName );
    $t->addCell( $form->emailFromEmail );

    return $t;
  }

  /**
   * Produce the reply to area of the mail from
   *
   * @return \Metrol\HTML\Table
   */
  public function getReplyToTable()
  {
    $form = $this->mailForm->getForm();

    $t = new \Metrol\HTML\Table('Reply To');

    $t->addRow(  $form->emailReplyToName->getLabel() );
    $t->addCell( $form->emailReplyToName );
    $t->addCell( $form->emailReplyToEmail );

    return $t;
  }

  /**
   * Produce the Organization Name field for the Email
   *
   * @return \Metrol\HTML\Table
   */
  public function getOrgNameTable()
  {
    $form = $this->mailForm->getForm();

    $t = new \Metrol\HTML\Table('Organization Name');

    $t->addRow( $form->emailOrgName->getLabel() );
    $t->addCell( $form->emailOrgName );

    return $t;
  }

  /**
   * Produce the recipient form table
   *
   * @return \Metrol\HTML\Div
   */
  public function getRecipientTable()
  {
    $form = $this->mailForm->getForm();

    $t = new \Metrol\HTML\Table('Recipients Table');
    $t->setID('recipTable');

    $recipIndexSet = $form->emailRecipientType->getTagSetKeys();

    foreach ( $recipIndexSet as $recipIndex )
    {
      $t->addRow( $form->emailRecipientType->getIndex($recipIndex));
      $t->addCell($form->emailRecipientName->getIndex($recipIndex));
      $t->addCell($form->emailRecipientEmail->getIndex($recipIndex));
      $t->addCell($form->emailDeleteRecipient);
    }

    // Default for an empty set
    if ( count($recipIndexSet) == 0 )
    {
      $t->addRow( $form->emailRecipientType->getIndex(0));
      $t->addCell($form->emailRecipientName->getIndex(0));
      $t->addCell($form->emailRecipientEmail->getIndex(0));
      $t->addCell($form->emailDeleteRecipient);
    }

    $t->setAfter( $form->emailAddRecipient );

    $div = new \Metrol\HTML\Div;
    $div->setID('recipients')
        ->setContent($t->output());

    return $div;
  }

  /**
   * Produce the subject area of the mail
   *
   * @return \Metrol\HTML\Table
   */
  public function getSubjectTable()
  {
    $form = $this->mailForm->getForm();

    $t = new \Metrol\HTML\Table('Subject');

    $t->addRow(  $form->emailSubject->getLabel() );
    $t->addCell( $form->emailSubject );

    return $t;
  }

  /**
   * Produce the message area of the mail
   *
   * @return \Metrol\HTML\Table
   */
  public function getMessageTable()
  {
    $form = $this->mailForm->getForm();

    $t = new \Metrol\HTML\Table('Email Message');

    $t->addRow( $form->emailBody->getLabel() );
    $t->addRow( $form->emailBody );

    return $t;
  }

  /**
   * Provide an area for the signature to the Email
   *
   * @return \Metrol\HTML\Table
   */
  public function getSignatureTable()
  {
    $form = $this->mailForm->getForm();

    $t = new \Metrol\HTML\Table('Signature');

    $t->addRow( $form->emailSignature->getLabel() );
    $t->addRow( $form->emailSignature );

    return $t;
  }

  /**
   * The form submit buttons
   *
   * @return \Metrol\HTML\Table
   */
  public function getSubmitButtonTable()
  {
    $form = $this->mailForm->getForm();

    $t = new \Metrol\HTML\Table('Form Submit');

    $t->addRow( $form->emailSubmit );

    return $t;
  }
}
