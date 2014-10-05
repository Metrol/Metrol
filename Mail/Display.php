<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Mail;

/**
 * Handles displaying an Email for diagnostic and visual needs.
 */
class Display
{
  /**
   * The Mail object to be displayed
   *
   * @var \Metrol\Mail
   */
  protected $mail;

  /**
   * The sent on date to display
   *
   * @var \Metrol\Date
   */
  protected $sentDate;

  /**
   * Initilizes the Display object
   *
   * @param \Metrol\Mail
   */
  public function __construct(\Metrol\Mail $mail)
  {
    $this->mail = $mail;

    $this->sentDate = null;
  }

  /**
   * Sets the date the message was sent on.
   * This value is not stored or used anywhere but for the message display.
   *
   * @param Metrol\Date
   */
  public function setSentDate(\Metrol\Date $sentDate)
  {
    $this->sentDate = $sentDate;

    return $this;
  }

  /**
   * Produce a reasonably usable bit of diagnostic output.
   *
   * @param boolean
   * @return string
   */
  public function __toString()
  {
    $rtn = "";

    $from = $this->mail->getFrom();

    $rtn .= 'From: ';
    $rtn .= $from->getFrom()."\n";

    if ( strlen($from->getOrgName()) > 0 )
    {
      $rtn .= 'Organization: ';
      $rtn .= $from->getOrgName()."\n";
    }

    $recipList = $this->mail->getRecipients()->getToList();

    foreach ( $recipList as $recip )
    {
      $rtn .= 'To: "'.$recip['name'].'" <'.$recip['email'].'>'."\n";
    }

    $recipList = $this->mail->getRecipients()->getCCList();

    foreach ( $recipList as $recip )
    {
      $rtn .= 'cc: "'.$recip['name'].'" <'.$recip['email'].'>'."\n";
    }

    $recipList = $this->mail->getRecipients()->getBCCList();

    foreach ( $recipList as $recip )
    {
      $rtn .= 'bcc: "'.$recip['name'].'" <'.$recip['email'].'>'."\n";
    }

    $content = $this->mail->getContent();

    $rtn .= 'Subject: '.$content->getSubject()."\n";
    $rtn .= "\n--------------------- Message Body --------------------------\n";
    $rtn .= $content->outputWrapped();
    $rtn .= "\n\n--\n";
    $rtn .= $from->getSignature();
    $rtn .= "\n---------------------- End Message --------------------------\n";

    $rtn = \Metrol\Text::htmlentbrk($rtn);

    return $rtn;
  }

  /**
   * Produces a "presentable" version of the contents in this class that can
   * be used in reports and such.
   * CSS Classes used:
   * Table:           MailTable
   * Thead area:      MailTableHead    <- Includes recipients, from, subject
   * From Table       MailTableFrom    <- The From and Reply-to area
   * Recipient Table: MailTableRecip   <- The table that contains recipients
   * Subject Row:     MailTableSubject
   * Tbody area:      MailTableBody    <- A layer containing the message body
   * Message Body:    MailTableMessage <- The table cell containing the message
   * Tfoot area:      MailTableFoot    <- An area for the signature
   *
   * @return \Metrol\HTML\Table
   */
  public function presentation()
  {
    $t = new \Metrol\HTML\Table;
    $t->setBorder(1);
    $t->setClass('MailTable');

    $t->setHeadActive()->getActiveSection()->setClass('MailTableHead');

    $this->presentationFromTable($t);
    $this->presentationRecipTable($t);

    $t->addRow('Subject:');
    $t->addCell( $this->mail->getContent()->getSubject() )
      ->setClass('MailTableSubject');

    $t->setBodyActive()->getActiveSection()->setClass('MailTableBody');

    $t->addRow();
    $t->addCell( $this->mail->getContent()->outputWrapped() )
      ->setColSpan(2)
      ->setClass('MailTableMessage');

    $t->setFootActive()->getActiveSection()->setClass('MailTableFoot');

    $sig = $this->mail->getFrom()->getSignature();
    $sig = nl2br( htmlentities($sig) );
    $t->addRow();
    $t->addCell( $sig )
      ->setColSpan(2);

    return $t;
  }

  /**
   * Provide the From and Reply-To in a table for use in the presentation
   * method.
   *
   * @param \Metrol\HTML\Table
   */
  protected function presentationFromTable(\Metrol\HTML\Table $t)
  {
    $from = $this->mail->getFrom();

    $t->addRow('From:');
    $t->addCell( htmlentities($from->getFrom()) )
      ->setClass('MailTableFrom');
  }

  /**
   * Provide the recipient list in a table for use in the presentation method.
   *
   * @param \Metrol\HTML\Table
   */
  protected function presentationRecipTable(\Metrol\HTML\Table $t)
  {
    $recipList = $this->mail->getRecipients()->getFormattedList();

    foreach ( $recipList as $rType => $recipients )
    {
      $t->addRow($rType.':');

      foreach ( $recipients as $recipient )
      {
        $t->addCell(htmlentities($recipient))
          ->setClass('MailTableRecip');
      }
    }
  }
}
