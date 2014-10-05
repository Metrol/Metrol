<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\HTML\Form\Input;

/**
 * Provides a form field specifically for measurement types.
 *
 */
class Measure extends Number
{
  /**
   * Define which measurement system will values be coming in from.  If the
   * measurement type is different, it will be converted "from" this system.
   *
   * @uses \Metrol\Measure
   * @var integer
   */
  protected $convertTo;

  /**
   * A flag to determine if the type suffix should go next to the form field
   *
   * @var boolean
   */
  protected $showMeasureType;

  /**
   * The measurement object that all the values, math, and conversions will go
   * through.
   *
   * @var \Metrol\Measure
   */
  protected $measure;

  /**
   * Initialize the Measure input field object
   *
   * @param string Name of the field
   */
  public function __construct($fieldName = null)
  {
    parent::__construct($fieldName);

    $this->measure         = new \Metrol\Measure(0, \Metrol\Measure::FEET);
    $this->convertTo       = \Metrol\Measure::US;
    $this->showMeasureType = true;
  }

  /**
   * Adjusts the suffix and prefix of the tag ahead of it being printed
   *
   * @return string
   */
  public function output()
  {
    $meas = clone $this->measure;

    $origVal = $this->getValue();

    $meas->setValue( $origVal );
    $meas->convert($this->convertTo);

    $this->setValue( round($meas->getValue(), 2) );

    if ( $this->showMeasureType )
    {
      $this->setSuffix('&nbsp;'.$meas->getSuffix() );
    }
    else
    {
      $this->setSuffix('');
    }

    $rtn = parent::output();

    $this->setValue($origVal);

    return $rtn;
  }

  /**
   * Sets the type of measurement this form field is for.  Set using type
   * constants from \Metrol\Measure.
   *
   * @param integer
   * @uses \Metrol\Measure
   * @return this
   */
  public function setMeasureType($measureType)
  {
    $mt = intval($measureType);

    switch ($mt)
    {
      case \Metrol\Measure::SQ_FEET:
        $this->measure->setType($mt);
        break;

      case \Metrol\Measure::SQ_METERS:
        $this->measure->setType($mt);
        break;

      case \Metrol\Measure::MILES:
        $this->measure->setType($mt);
        break;

      case \Metrol\Measure::KILOMETERS:
        $this->measure->setType($mt);
        break;

      case \Metrol\Measure::FEET:
        $this->measure->setType($mt);
        break;

      case \Metrol\Measure::METERS:
        $this->measure->setType($mt);
        break;

      default:
        $this->measure->setType(\Metrol\Measure::FEET);
        break;
    }

    return $this;
  }

  /**
   * Sets the flag to determine if the measurement type should go next to the
   * form field.
   *
   * @param boolean
   * @return self
   */
  public function setShowMeasureType($showMeasureType = true)
  {
    if ( $showMeasureType )
    {
      $this->showMeasureType = true;
    }
    else
    {
      $this->showMeasureType = false;
    }

    return $this;
  }

  /**
   * Specifies which kind of measurement will be used for the display
   *
   * @param integer
   * @return this
   */
  public function setConvertTo($measureSystem)
  {
    $this->convertTo = $measureSystem;

    return $this;
  }
}
