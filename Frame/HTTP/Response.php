<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTTP;

/**
 * Description of Response
 */
class Response extends \Metrol\Frame\Response
{
  /**
   * Common HTTP Status codes
   *
   * @const
   */
  const OK           = 200;
  const BAD_REQUEST  = 400;
  const UNAUTHORIZED = 401;
  const FORBIDDEN    = 403;
  const NOT_FOUND    = 404;

  /**
   * Stack of entries to go into the response header
   *
   * @var array
   */
  protected $headers;

  /**
   * Initilizes the Response object
   *
   * @param object
   */
  public function __construct()
  {
    parent::__construct();

    $this->headers = array();
  }

  /**
   * Extend the parent method to include writing HTTP headers out before the
   * actual output.
   *
   * @return string
   */
  public function __toString()
  {
    $exitFlag = false;

    foreach ( $this->headers as $header )
    {
      $headerText = $header['header'];

      if ( strtolower(substr($headerText, 0, 8)) == 'location' )
      {
        $exitFlag = true;
      }

      header($headerText, $header['replace']);

      if ( $exitFlag )
      {
        exit;
      }
    }

    return parent::__toString();
  }

  /**
   * Adds a header to the stack.
   * For HTTP/ headers the value is ignored, only the key is added.
   * For everything else, the header string becomes $key: $value
   *
   * @param string The key for the header
   * @param string The value of the header
   * @param boolean Determines if this should overwrite a previous header
   * @return this
   */
  public function addHeader($key, $value = '', $replace = true)
  {
    if ( $replace )
    {
      $repFlag = true;
    }
    else
    {
      $repFlag = false;
    }

    $idx = count($this->headers);

    if ( strtolower(substr($key, 0, 5)) == 'http/')
    {
      $header = $key;
    }
    else
    {
      $header = $key.': '.$value;
    }

    $this->headers[$idx]['header']  = $header;
    $this->headers[$idx]['replace'] = $repFlag;

    return $this;
  }

  /**
   * Takes in a redirect request and adds it to the header output
   *
   * @param string Path or URL to redirect to
   * @param HTTP Status code
   */
  public function redirect($location, $status = 200)
  {
    header('Location: '.$location, intval($status));
    exit;
  }
}
