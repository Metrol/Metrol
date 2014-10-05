<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Mail;

/**
 * Stores all the fields that will be put into the headers area of an Email
 */
class Headers implements \Iterator
{
  /**
   * The version of MIME supported by this class
   *
   * @const
   */
  const MIME_VERSION = '1.0';

  /**
   * List of headers keyed by the header name
   *
   * @var array
   */
  private $fields;

  /**
   * Initilizes the Headers object
   *
   * @param object
   */
  public function __construct()
  {
    $this->fields = array();

    $this->add('MIME-Version', self::MIME_VERSION);
    $this->add('Content-type', 'text/plain; charset="UTF-8"');
  }

  /**
   * Produces the output of all the headers in a string suitable for going into
   * an Email
   *
   * @return string
   */
  public function output()
  {
    $rtn = '';

    foreach ( $this as $field => $value )
    {
      $rtn .= $field.': '.$value.PHP_EOL;
    }

    return $rtn;
  }

  /**
   * Adds or replaces a header field
   *
   * @param string
   * @param string
   * @return this
   */
  public function add($headerField, $value)
  {
    $this->fields[$headerField] = $value;

    return $this;
  }

  /**
   * Sets the organization name to be included in the header fields which is
   * seen by most modern EMail clients.
   *
   * @param string
   * @return this
   */
  public function setOrganization($orgName)
  {
    $this->add('Organization', $orgName);

    return $this;
  }

  /**
   * Sets the name of the mailer application header field.
   *
   * @param string
   * @return this
   */
  public function setMailerApp($appName)
  {
    $this->add('X-Mailer', $appName);

    return $this;
  }

  /**
   * Sets who the mail is from.
   *
   * This method makes the assumption that the string being passed in is
   * already properly formatted with the name and address.
   *
   * @param string
   * @return this
   */
  public function setFrom($from)
  {
    $this->add('From', $from);

    return $this;
  }

  /**
   * Sets the name and address the mail should be reply to
   *
   * This method makes the assumption that the string being passed in is
   * already properly formatted with the name and address.
   *
   * @param string
   * @return this
   */
  public function setReplyTo($replyTo)
  {
    $this->add('Reply-To', $replyTo);

    return $this;
  }

  /**
   * Will set the content of the Email as HTML
   *
   * @param boolean
   * @return this
   */
  public function htmlContent($flag)
  {
    if ( $flag )
    {
      $this->add('Content-type', 'text/html; charset="UTF-8"');
    }
    else
    {
      $this->add('Content-type', 'text/plain; charset="UTF-8"');
    }
  }

  /**
   * Needed to determine if the Email content was plain text or HTML
   *
   * @return boolean
   */
  public function isContentHTML()
  {
    $rtn = false;

    if ( $this->fields['Content-type'] == 'text/html; charset="UTF-8"' )
    {
      $rtn = true;
    }

    return $rtn;
  }

  /**
   * Deletes a specific header field
   *
   * @param string
   * @return this
   */
  public function delete($headerField)
  {
    if ( array_key_exists($headerField, $this->fields) )
    {
      unset($this->fields[$headerField]);
    }

    return $this;
  }

  /**
   * Clears out all the headers that have been set.
   *
   * @return this
   */
  public function clear()
  {
    $this->fields = array();
  }

  /**
   * Implementing the Iterartor interface to walk through the headers
   */
  public function rewind()
  {
    \reset($this->fields);
  }

  public function current()
  {
    return \current($this->fields);
  }

  public function key()
  {
    return \key($this->fields);
  }

  public function next()
  {
    return \next($this->fields);
  }

  public function valid()
  {
    return $this->current() !== false;
  }
}
