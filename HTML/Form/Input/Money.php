<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form\Input;

/**
 * An input box for entering money values
 *
 */
class Money extends Number
{
  /**
   * Default currency to use when nothing is specified
   *
   * @const
   */
  const DEF_CURRENCY = 'USD';

  /**
   * The currency to use for this form field
   *
   * @var string
   */
  protected $currency;

  /**
   * The date the value of the currency is valid on.  This is used to
   * properly convert to various currencies.
   *
   * @var \Metrol\Date
   */
  protected $exchDate;

  /**
   * A flag to determine if the currency type should go next to the form field
   *
   * @var boolean
   */
  protected $showCurrency;

  /**
   * A flag to determine if a currency's symbol should be used or not
   *
   * @var boolean
   */
  protected $showSymbol;

  /**
   * Initialize the Phone object
   *
   * @param string Name of the field
   */
  public function __construct($fieldName = null)
  {
    parent::__construct($fieldName);

    $this->showCurrency = false;
    $this->showSymbol   = true;
    $this->currency     = self::DEF_CURRENCY;
    $this->exchDate     = new \Metrol\Date;
  }

  /**
   * Adjusts the suffix and prefix of the tag ahead of it being printed
   *
   * @return string
   */
  public function output()
  {
    if ( $this->showCurrency )
    {
      $span = new \Metrol\HTML\Span;
      $span->setClass('currencyCode')
           ->setContent($this->currency);

      $this->setSuffix('&nbsp;'.$span);
    }
    else
    {
      $this->setSuffix('');
    }

    if ( $this->showSymbol )
    {
      $symbol = \Metrol\Money::getHtmlSymbol($this->currency);

      $span = new \Metrol\HTML\Span;
      $span->setClass('currencySymbol')
           ->setContent($symbol);

      $this->setPrefix($span.'&nbsp;');
    }
    else
    {
      $this->setPrefix('');
    }

    return parent::output();
  }

  /**
   * Sets the currency this field is using
   *
   * @param string
   * @return this
   */
  public function setCurrency($currencyCode)
  {
    if ( \Metrol\Money::isCurrency($currencyCode) )
    {
      $this->currency = $currencyCode;
    }

    return $this;
  }

  /**
   * Sets the date the value is valid for.
   *
   * This class does NOT perform any kind of conversion between currencies.
   * However, the value, currency, and date can be used by a conversion routine
   * to perform that task.
   *
   * @param \Metrol\Date
   * @return this
   */
  public function setExchangeDate(\Metrol\Date $exchDate)
  {
    $this->exchDate = $exchDate;

    return $this;
  }

  /**
   * Sets the flag to determine if the currency type should go next to the
   * form field.
   *
   * @param boolean
   * @return self
   */
  public function setShowCurrency($showCurrency = true)
  {
    if ( $showCurrency )
    {
      $this->showCurrency = true;
    }
    else
    {
      $this->showCurrency = false;
    }

    return $this;
  }

  /**
   * Sets the flag to determine if a currency's symbol should be used or not.
   *
   * @param boolean
   * @return self
   */
  public function setShowSymbol($showSymbol = true)
  {
    if ( $showSymbol )
    {
      $this->showSymbol = true;
    }
    else
    {
      $this->showSymbol = false;
    }

    return $this;
  }
}
