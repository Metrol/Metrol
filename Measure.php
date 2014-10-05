<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol;

/**
 * Handle basic measurement conversions and displays for standard and metric
 * measurements.
 *
 */
class Measure
{
  /**
   * Define which measurement systems are supported.
   *
   * @const
   */
  const US     = 0;
  const METRIC = 1;

  /**
   * Definitions for field types as to what is being stored in them.
   *
   * @const
   */
  const SQ_FEET    = 1;
  const SQ_METERS  = 2;
  const MILES      = 3;
  const KILOMETERS = 4;
  const FEET       = 5;
  const METERS     = 6;

  /**
   * How many digits will be taken in account in the actual calculations
   *
   * @var integer
   */
  const CALC_SCALE = 10;

  /**
   * The conversion values used throughout this class.
   *
   * @const
   */
  const SQ_FEET_TO_METERS = ".09290304";
  const SQ_METERS_TO_FEET = "10.76391041670972";

  const METERS_TO_FEET    = "3.2808399";
  const FEET_TO_METERS    = ".3048";

  const MILES_TO_KM       = "1.609344";
  const KM_TO_MILES       = ".621371192";

  /**
   * The value of the measurement in this class.  Stored as a string to maintain
   * precision and for use in BC Math functions.
   *
   * @var string
   */
  private $measureValue;

  /**
   * What kind of measurement type this is
   *
   * @var string
   */
  private $measType;

  /**
   * What to show for a suffix at the end of the formatted display.
   * Normally this is automatically set by the setType() method, but may be
   * overridden.
   *
   * @var string
   */
  private $measureSuffix;

  /**
   * How many decimal digits to display on the output
   *
   * @var integer
   */
  private $precisionVal = 0;

  /**
   * List of all the measurement types and their associated data.
   *
   * @static
   */
  private static $mTypes = array();

  /**
   * Cross reference for what type of measurement value is left after a
   * conversion.
   * fmt: arr['METERS_TO_FEET'] = self::FEET;
   * @static
   */
  private static $convertedMeasType = array();

  /**
   * Sets the starting value and the measurement system to be used.
   * 0 = US, 1 = Metric
   *
   * @param float Amount to start with
   * @param integer Type of measurement
   */
  public function __construct($value = 0, $measurementType = 1)
  {
    self::initMeasurementTypes();

    $this->measureValue = (string) $value;

    $this->setType($measurementType);
  }

  /**
   * Provide the fully formatted output of the measurement value
   *
   * @return string
   */
  public function __toString()
  {
    return $this->formattedOutput();
  }

  /**
   * Assembles the ratios into an array that can be readily accessed
   *
   */
  static private function initMeasurementTypes()
  {
    // Only need to load this array once.
    if ( count(self::$mTypes) ) { return; }

    self::$mTypes[self::FEET]['suffix']           = 'ft';
    self::$mTypes[self::FEET][self::US]           = 1;
    self::$mTypes[self::FEET][self::METRIC]       = self::FEET_TO_METERS;
    self::$mTypes[self::FEET]['convertType']      = self::METERS;

    self::$mTypes[self::SQ_FEET]['suffix']        = 'sq ft';
    self::$mTypes[self::SQ_FEET][self::US]        = 1;
    self::$mTypes[self::SQ_FEET][self::METRIC]    = self::SQ_FEET_TO_METERS;
    self::$mTypes[self::SQ_FEET]['convertType']   = self::SQ_METERS;

    self::$mTypes[self::MILES]['suffix']          = 'miles';
    self::$mTypes[self::MILES][self::US]          = 1;
    self::$mTypes[self::MILES][self::METRIC]      = self::MILES_TO_KM;
    self::$mTypes[self::MILES]['convertType']     = self::KILOMETERS;

    self::$mTypes[self::METERS]['suffix']         = 'meters';
    self::$mTypes[self::METERS][self::US]         = self::METERS_TO_FEET;
    self::$mTypes[self::METERS][self::METRIC]     = 1;
    self::$mTypes[self::METERS]['convertType']    = self::FEET;

    self::$mTypes[self::SQ_METERS]['suffix']      = 'sq meters';
    self::$mTypes[self::SQ_METERS][self::US]      = self::SQ_METERS_TO_FEET;
    self::$mTypes[self::SQ_METERS][self::METRIC]  = 1;
    self::$mTypes[self::SQ_METERS]['convertType'] = self::SQ_FEET;

    self::$mTypes[self::KILOMETERS]['suffix']      = 'km';
    self::$mTypes[self::KILOMETERS][self::US]      = self::KM_TO_MILES;
    self::$mTypes[self::KILOMETERS][self::METRIC]  = 1;
    self::$mTypes[self::KILOMETERS]['convertType'] = self::MILES;
  }

  /**
   * Provides an array with the measurement systems.
   * Suitable for use in a dropdown form object.
   *
   * @return array
   */
  static public function getSystemList()
  {
    $sl = array(self::US     => "US Standard",
                self::METRIC => "Metric");

    return $sl;
  }

  /**
   * For the measurement type in use, provide which measurement system it is
   * for.
   *
   * @return integer US|Metric
   */
  public function getMeasureSystem()
  {
    $rtn = self::US;

    if ( self::$mTypes[$this->measType][self::METRIC] === 1 )
    {
      $rtn = self::METRIC;
    }

    return $rtn;
  }

  /**
   * Provide the equivalent type for the specified one.
   * For example, FEET would have an equivalent type of METERS
   *
   * @param integer
   * @return integer
   */
  static public function equivalentType($typeFrom)
  {
    self::initMeasurementTypes();

    if ( !array_key_exists($typeFrom, self::$mTypes) ) { return; }

    return self::$mTypes[$typeFrom]['convertType'];
  }

  /**
   * Converts the value and type of the object to either US or Metric.
   * Use the class constants US or METRIC to specifiy.
   *
   * @param integer
   * @return this
   */
  public function convert($toType)
  {
    switch ($toType)
    {
      case 'US':
        $this->convertToUS();
        break;

      case 'US Standard':
        $this->convertToUS();
        break;

      case 'Metric':
        $this->convertToMetric();
        break;

      case self::US:
        $this->convertToUS();
        break;

      case self::METRIC:
        $this->convertToMetric();
        break;
    }

    return $this;
  }

  /**
   * Converts the value stored in this method to the US equivalent
   *
   * @return this
   */
  private function convertToUS()
  {
    // Something like this might be better to throw an exception
    if ( !array_key_exists($this->measType, self::$mTypes) )
    {
      return $this;
    }

    // Don't do anything if we're already in a US based measurement
    if ( self::$mTypes[$this->measType][self::US] == 1 )
    {
      return;
    }

    $conversion = self::$mTypes[$this->measType][self::US];

    $newVal = bcmul($this->measureValue, $conversion, self::CALC_SCALE);
    $this->measureValue = $newVal;
    $this->setType(self::$mTypes[$this->measType]['convertType']);

    return $this;
  }

  /**
   * Converts the value stored in this method to the US equivalent
   *
   * @return this
   */
  private function convertToMetric()
  {
    // Something like this might be better to throw an exception
    if ( !array_key_exists($this->measType, self::$mTypes) )
    {
      return $this;
    }

    // Don't do anything if we're already in a Metric based measurement
    if ( self::$mTypes[$this->measType][self::METRIC] == 1 )
    {
      return;
    }

    $conversion = self::$mTypes[$this->measType][self::METRIC];
    $precision  = self::CALC_SCALE;
    $value      = $this->measureValue;

    $newVal = bcmul($this->measureValue, $conversion, self::CALC_SCALE);

    $this->measureValue = $newVal;
    $this->setType(self::$mTypes[$this->measType]['convertType']);

    return $this;
  }

  /**
   * Sets the value of the measurement in this class.
   *
   * @param float
   * @return this
   */
  public function setValue($val)
  {
    $this->measureValue = $val;

    return $this;
  }

  /**
   * Gets just the raw value of the measurement.
   * If an argument is specified, the value returned will be rounded to that
   * number of decimal places.
   *
   * @param integer
   * @return float
   */
  public function getValue($precision = null)
  {
    if ( $precision === null )
    {
      return floatval($this->measureValue);
    }

    return round( floatval($this->measureValue), intval($precision) );
  }

  /**
   * Sets the measurement type in use for this class.
   * Refer to the class constants for valid measurement types.
   *
   * @param integer
   * @return this
   */
  public function setType($measurementType)
  {
    $mt = intval($measurementType);

    if ( !array_key_exists($mt, self::$mTypes) )
    {
      return;
    }

    $this->measType = $mt;
    $this->measureSuffix = self::$mTypes[$mt]['suffix'];

    return $this;
  }

  /**
   * Override the default suffix used in the formatted output.
   *
   * @param string
   * @return this
   */
  public function setSuffix($suffix)
  {
    $this->measureSuffix = $suffix;

    return $this;
  }

  /**
   * @return string
   */
  public function getSuffix()
  {
    return $this->measureSuffix;
  }

  /**
   * Defines the number of decimal places to show for the output.
   * This only affects the output.  All calculations are performed with the
   * maximum precision the php.ini configuration allows.
   *
   * Passing in a null value for the parameter here will cause the output to
   * include all the decimals.
   *
   * @param integer
   * @return this
   */
  public function setPrecision($prec = null)
  {
    if ( $prec === null )
    {
      $this->precisionVal = null;

      return;
    }

    $this->precisionVal = intval($prec);

    return $this;
  }

  /**
   * Due to problems with PHP's number_format rounding whether I want to or
   * not, this replacement routine will add the appropriate commas and respect
   * the precision value stored in this class.
   *
   * @return string
   */
  public function formattedOutput()
  {
    $value = $this->measureValue;

    if ( $this->precisionVal !== null )
    {
      $valStr = number_format(floatval($value), $this->precisionVal);
    }
    elseif ( $value == intval($value) )
    {
      $valStr = number_format($value, $this->precisionVal);
    }
    else
    {
      $x = $value;

      $xs = (string) $x;
      $xInt = intval($x);
      $xDec = substr($xs, strpos($xs, "."));

      $y = array_reverse(str_split($xInt));

      $res = array();

      foreach ( $y as $i => $n )
      {
        $res[] = $n;

        if ( ($i+1) / 3 === intval(($i+1) / 3) )
        {
          if ( $i+1 < count($y))
          {
            $res[] = ",";
          }
        }
      }

      $valStr = implode(array_reverse($res)).$xDec;
    }

    return $valStr." ".$this->measureSuffix;
  }
}
