<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form;

/**
 * Define the opening form tag
 */
class Open extends Tag
{
  /**
   * Default. All characters are encoded before sent (spaces are converted
   * to "+" symbols, and special characters are converted to ASCII HEX values)
   *
   * @const
   */
  const ENCODE_APPLICATION = 0;

  /**
   * No characters are encoded. This value is required when you are using
   * forms that have a file upload control
   *
   * @const
   */
  const ENCODE_MULTIPART = 1;

  /**
   * Spaces are converted to "+" symbols, but no special characters are encoded
   *
   * @const
   */
  const ENCODE_PLAIN = 2;

  /**
   */
  public function __construct()
  {
    parent::__construct('form', self::CLOSE_NONE);
  }

  /**
   * Sets the destination URL
   *
   * @param string
   * @return this
   */
  public function setActionURL($urlText)
  {
    $this->attribute()->action = $urlText;

    return $this;
  }

  /**
   * Sets the actions with a route name and paramters
   *
   * @param string Name of the route
   * @param array List of route arguments
   * @return this
   */
  public function setActionRoute($routeName, array $args)
  {
    $route = \Metrol\Frame\HTTP\Route\Cache::getInstance()
            ->getRoute($routeName);

    if ( $route == null )
    {
      $this->attribute()->action = './';

      return $this;
    }

    if ( is_array($args) )
    {
      $route->clearArguments();

      foreach ( $args as $arg )
      {
        $route->addArguments($arg);
      }
    }

    $this->attribute()->action = $route->getURL();

    return $this;
  }

  /**
   * Set the method to send the data.  Either POST or GET.
   *
   * @param string
   * @return this
   */
  public function setMethod($formMethod)
  {
    $method = strtolower($formMethod);

    if ( $method != 'post' and $method != 'get' )
    {
      $method = 'post';
    }

    $this->attribute()->method = $method;

    return $this;
  }

  /**
   * Set the encoding type
   *
   * @param integer
   * @return this
   */
  public function setEncoding($type)
  {
    $type = intval($type);
    $encType = null;

    switch ($type)
    {
      case self::ENCODE_APPLICATION:
        $encType = 'application/x-www-form-urlencoded';
        break;

      case self::ENCODE_MULTIPART:
        $encType = 'multipart/form-data';
        break;

      case self::ENCODE_PLAIN:
        $encType = 'text/plain';
        break;
    }

    if ( $encType !== null )
    {
      $this->attribute()->enctype = $encType;
    }

    return $this;
  }

  /**
   * Set the target to a new window
   *
   * @return this
   */
  public function newWindow()
  {
    $this->setTarget('_blank');

    return $this;
  }

  /**
   * Set the target to within the existing frame
   *
   * @return this
   */
  public function sameFrame()
  {
    $this->setTarget('_self');

    return $this;
  }

  /**
   * Set the target to the top most window that is open
   *
   * @return this
   */
  public function topWindow()
  {
    $this->setTarget('_top');

    return $this;
  }

  /**
   * Set the target to the immediate parent's frame or window
   *
   * @return this
   */
  public function parentFrame()
  {
    $this->setTarget('_parent');

    return $this;
  }

  /**
   * Actually sets the target
   *
   * @return this
   */
  private function setTarget($targetName)
  {
    $this->attribute()->target = $targetName;

    return $this;
  }
}
