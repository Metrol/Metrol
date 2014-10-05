<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Mail;

/**
 * Holds the various kinds of Email recipients for the Metrol\Mail class
 */
class Recipients
{
  /**
   * List of recipients an Email is TO.
   *
   * format: var[index]['name'] = "Bob Smith";
   * format: var[index]['email'] = "user@somewhere.com";
   *
   * @var array
   */
  protected $to;

  /**
   * List of recipients to carbon copy (CC) an Email to.
   *
   * format: var[index]['name'] = "Bob Smith";
   * format: var[index]['email'] = "user@somewhere.com";
   *
   * @var array
   */
  protected $cc;

  /**
   * List of recipients to blind carbon copy (BCC) an Email to.
   *
   * format: var[index]['name'] = "Bob Smith";
   * format: var[index]['email'] = "user@somewhere.com";
   *
   * @var array
   */
  protected $bcc;

  /**
   * List of recipients to be sent a 2nd copy of the Email.
   *
   * format: var[index]['name'] = "Bob Smith";
   * format: var[index]['email'] = "user@somewhere.com";
   *
   * @var array
   */
  protected $copyTo;

  /**
   * The Email validation object to be used on every address passed into here.
   *
   * @var Validate
   */
  protected $eVal;

  /**
   * Initilizes the Recpients object
   */
  public function __construct()
  {
    $this->clearAll();

    $this->eVal = new Validate();
  }

  /**
   * Dumps values stored here out.
   */
  public function debug()
  {
    print "TO List:\n";
    var_dump($this->to);

    print "CC List:\n";
    var_dump($this->cc);

    print "BCC List:\n";
    var_dump($this->bcc);
  }

  /**
   * Clears all the recipient types
   *
   * @return this
   */
  public function clearAll()
  {
    $this->to     = array();
    $this->cc     = array();
    $this->bcc    = array();
    $this->copyTo = array();
  }

  /**
   * Adds a recipient to the TO list
   *
   * @param string EMail address
   * @param string Name of the person
   */
  public function addTo($email, $name = "")
  {
    $this->addRecipient($email, $name, 'to');

    return $this;
  }

  /**
   * Adds a recipient to the CC list
   *
   * @param string EMail address
   * @param string Name of the person
   */
  public function addCc($email, $name = "")
  {
    $this->addRecipient($email, $name, 'cc');

    return $this;
  }

  /**
   * Adds a recipient to the BCC list
   *
   * @param string EMail address
   * @param string Name of the person
   */
  public function addBcc($email, $name = "")
  {
    $this->addRecipient($email, $name, 'bcc');

    return $this;
  }

  /**
   * Provide the list of people the mail is TO
   *
   * @return array
   */
  public function getToList()
  {
    return $this->to;
  }

  /**
   * Provide the list of people the mail is CC'd to
   *
   * @return array
   */
  public function getCCList()
  {
    return $this->cc;
  }

  /**
   * Provide the list of people the mail is TO
   *
   * @return array
   */
  public function getBCCList()
  {
    return $this->bcc;
  }

  /**
   * Provide all the recipients in an array with the names and email addresses
   * formatted.
   *
   * @return array
   */
  public function getFormattedList()
  {
    $rtn = array();

    foreach ( $this->to as $recip )
    {
      $recipFmt = '';

      if ( strlen($recip['name']) > 0 )
      {
        $recipFmt = '"'.$recip['name'].'" ';
      }

      $recipFmt .= '<'.$recip['email'].'>';

      $rtn['To'][] = $recipFmt;
    }

    foreach ( $this->cc as $recip )
    {
      $recipFmt = '';

      if ( strlen($recip['name']) > 0 )
      {
        $recipFmt = '"'.$recip['name'].'" ';
      }

      $recipFmt .= '<'.$recip['email'].'>';

      $rtn['cc'][] = $recipFmt;
    }

    foreach ( $this->bcc as $recip )
    {
      $recipFmt = '';

      if ( strlen($recip['name']) > 0 )
      {
        $recipFmt = '"'.$recip['name'].'" ';
      }

      $recipFmt .= '<'.$recip['email'].'>';

      $rtn['bcc'][] = $recipFmt;
    }

    return $rtn;
  }

  /**
   * Get a list of all the recipients back.  The order will always begin with
   * the "To", then "cc", followed by "bcc".
   *
   * The array format will be as follows
   *
   * $arr['To'][idx] = $obj
   *
   * The object will contain a name and email variable.
   *
   * @return array
   */
  public function getAllRecipients()
  {
    $rtn = array();

    foreach ( $this->to as $recip )
    {
      $obj = new \stdClass;
      $obj->type  = 'To';
      $obj->name  = $recip['name'];
      $obj->email = $recip['email'];

      $rtn[] = $obj;
    }

    foreach ( $this->cc as $recip )
    {
      $obj = new \stdClass;
      $obj->type  = 'cc';
      $obj->name  = $recip['name'];
      $obj->email = $recip['email'];

      $rtn[] = $obj;
    }

    foreach ( $this->bcc as $recip )
    {
      $obj = new \stdClass;
      $obj->type  = 'bcc';
      $obj->name  = $recip['name'];
      $obj->email = $recip['email'];

      $rtn[] = $obj;
    }

    return $rtn;
  }

  /**
   * When this is set, causes the mail to be sent a 2nd time to the specified
   * addresses.  Mail must be enabled for this to have any affect.  An empty
   * string turns this off.
   *
   * This is a totally different concept than cc or bcc.  This is meant for
   * archival or troubleshooting purposes.
   *
   * @param string
   * @param string
   * @return this
   */
  public function addCopyTo($email, $name)
  {
    // Make sure we've got a 100% pure RFC-3696 compliant Email Address
    if ( !$this->eVal->setEmail($email)->validate() )
    {
      return $this;
    }

    $this->copyTo[]['name']  = substr($name, 0, 255);
    $this->copyTo[]['email'] = $email;

    return $this;
  }

  /**
   * Provides the first TO address that was added, if one exists
   *
   * @return string
   */
  public function getMailTo()
  {
    $rtn = '';

    if ( count($this->to) > 0 )
    {
      $toInfo = $this->to[0];

      if ( array_key_exists('name', $toInfo) )
      {
        if ( strlen($toInfo['name']) > 0 )
        {
          $rtn .= '"'.$toInfo['name'].'" ';
        }
      }

      if ( array_key_exists('email', $toInfo) )
      {
        $rtn .= '<'.$toInfo['email'].'>';
      }
    }

    return $rtn;
  }

  /**
   * Determines if there's enough information here for an Email to send.
   * In this case, just need at least one person in the To list.
   *
   * @return boolean
   */
  public function readyToSend()
  {
    $rtn = false;

    $to = $this->getMailTo();

    if ( strlen($to) > 0 )
    {
      $rtn = true;
    }

    return $rtn;
  }

  /**
   * Puts all of the recipients into the header object passed in
   *
   * @param \Metrol\Mail\Headers
   */
  public function populateHeaders(Headers $headers)
  {
    $to = '';
    $cc = '';
    $bcc = '';

    // Walk through the TO list
    foreach ( $this->to as $i => $address )
    {
      if ( $i == 0 )
      {
        continue; // Only the additional addresses are added
      }

      $name  = $address['name'];
      $email = $address['email'];

      if ( strlen($name) > 0 )
      {
        $to .= '"'.$name.'" ';
      }

      $to .= '<'.$email.'>, ';
    }

    // Walk through the CC list
    foreach ( $this->cc as $i => $address )
    {
      $name  = $address['name'];
      $email = $address['email'];

      if ( strlen($name) > 0 )
      {
        $cc .= '"'.$name.'" ';
      }

      $cc .= '<'.$email.'>, ';
    }

    // Walk through the BCC list
    foreach ( $this->bcc as $i => $address )
    {
      $name  = $address['name'];
      $email = $address['email'];

      if ( strlen($name) > 0 )
      {
        $bcc .= '"'.$name.'" ';
      }

      $bcc .= '<'.$email.'>, ';
    }

    if ( strlen($to) > 0 )
    {
      $to = substr($to, 0, -2);
      $headers->add('To', $to);
    }

    if ( strlen($cc) > 0 )
    {
      $cc = substr($cc, 0, -2);
      $headers->add('CC', $cc);
    }

    if ( strlen($bcc) > 0 )
    {
      $bcc = substr($bcc, 0, -2);
      $headers->add('BCC', $bcc);
    }
  }

  /**
   * Handles actually adding a recipent to the specified type of recipient
   *
   * @param string Email address
   * @param string Name of the recipient
   * @param string The kind of recipient
   */
  protected function addRecipient($email, $name, $recipientType)
  {
    $types = array('to', 'cc', 'bcc');

    // Double checking we've got a valid recipient type
    if ( in_array(strtolower($recipientType), $types) )
    {
      $rt = strtolower($recipientType);
    }
    else
    {
      return;
    }

    $email = substr($email, 0, 255); // 255 is the max per RFC-3696

    // If the email address is already in any of the lists, don't add it again
    if ( $this->inList($email) )
    {
      return;
    }

    // Make sure we've got a 100% pure RFC-3696 compliant Email Address
    if ( !$this->eVal->setEmail($email)->validate() )
    {
      return;
    }

    $list = &$this->$rt;

    $i = count($list);
    $list[$i]['name']  = $name;
    $list[$i]['email'] = $email;
  }

  /**
   * Checks all the address lists to see if the passed in EMail address
   * already exists in any of them.
   *
   * @param string EMail address in question
   * @return boolean True if found in a list
   */
  protected function inList($email)
  {
    $inListFlag = FALSE;
    $email = strtolower($email);

    $lists = array();
    $lists[] = &$this->to;
    $lists[] = &$this->cc;
    $lists[] = &$this->bcc;

    foreach ( $lists as $list )
    {
      foreach ($list as $nameAddr)
      {
        $listEmail = strtolower($nameAddr["email"]);

        if ( $listEmail == $email )
        {
          $inListFlag = TRUE;
          break;
        }
      }
    }

    return $inListFlag;
  }
}
