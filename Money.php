<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol;

/**
 * Instead of ints and floats, Metrol_Money can handle all the basic math for
 * cash.
 */
class Money
{
  const DEF_PRECISION = 0;
  const DEF_CURRENCY  = 'USD';

  /**
   * The current amount being stored.
   * This amount will always be in US Dollars then converted as needed to other
   * currencies.
   *
   * @var float
   */
  private $amountValue;

  /**
   * Currency code of the value being stored.
   *
   * @var string
   */
  private $currencyCode;

  /**
   * Maintain a cross reference of currency symbols as HTML values
   *
   * @var array
   */
  static private $htmlSymbols;

  /**
   * Maintain a cross reference between the currency codes and their full
   * name.
   *
   * @var array
   */
  static private $currencyNames;

  /**
   * A list of all the number formats for the supported currencies.
   * example, formats["USD"] = $ ###,###.##
   *
   * @var array
   */
  static private $spreadSheetFormats;

  /**
   * How many digits of precision are requested.
   *
   * @var integer
   */
  private $precision;

  /**
   * Whether or not to show the currency code as a suffix.
   *
   * @var boolean
   */
  private $suffixFlag;

  /**
   * @param float Amount to start with
   * @param string The currency code
   */
  public function __construct($amount = 0, $currency = null)
  {
    $this->precision  = self::DEF_PRECISION;
    $this->suffixFlag = true;

    self::initCurrencies();
    self::initHtmlSymbols();

    if ( $currency === null )
    {
      $currency = self::DEF_CURRENCY;
    }

    $this->setAmount($amount, $currency);
  }

  /**
   * Provides the properly formatted output from this object ready to display
   *
   * @return string
   */
  public function __toString()
  {
    return $this->formattedHtmlAmount($this->amountValue, $this->currencyCode);
  }

  /**
   * Mostly used to provide a reasonable way to get to the amount stored here
   * to do some math to it.
   *
   * @param string
   * @return float
   */
  public function __get($var)
  {
    if ( $var == 'amount' )
    {
      return $this->amountValue;
    }
  }

  /**
   * Way to get the amount back in
   *
   * @param string
   * @param float
   */
  public function __set($var, $val)
  {
    if ( $var == 'amount' )
    {
      $this->setAmount($val);
    }
  }

  /**
   * Enables/Disables the currency code suffix for the formatted output.
   *
   * @param boolean
   * @return this
   */
  public function showSuffix($flag = TRUE)
  {
    if ( $flag )
    {
      $this->suffixFlag = TRUE;
    }
    else
    {
      $this->suffixFlag = FALSE;
    }

    return $this;
  }

  /**
   * Takes the decimal value of a percentage and adds it to the amount.
   * For example, to add a 15% tax to the amount you would say:
   * $money->percentInc(.15);
   *
   * @param float
   * @return float The new amount value
   */
  public function percentInc($per)
  {
    $this->amountValue = $this->amountValue + ($this->amountValue * $per);

    return $this->amountValue;
  }

  /**
   * Takes the decimal value of a percentage and subtracts it from the amount.
   * For example, to give a 12% discount to the amount you would say:
   * $money->percentDec(.12);
   *
   * @param float
   * @return float The new amount value
   */
  public function percentDec($per)
  {
    $this->amountValue = $this->amountValue - ($this->amountValue * $per);

    return $this->amountValue;
  }

  /**
   * Provide the long name of a currency based on its code.
   * Will provide back the name of the currency presently in use or a specified
   * currency code if provided.
   *
   * @param string
   * @return string
   */
  public static function getCurrencyName($currencyCode)
  {
    $rtn = '';

    if ( array_key_exists($currencyCode, self::$currencyNames) )
    {
      $rtn = self::$currencyNames[$currencyCode];
    }

    return $rtn;
  }

  /**
   * Provide the array of currency names with the currency code as the key.
   * Handy for drop down lists.
   *
   * @return array
   */
  public static function getCurrencyNameList()
  {
    $names = array();

    foreach ( self::$currencyNames AS $currencyCode => $currencyName )
    {
      $names[$currencyCode] = $currencyName." [$currencyCode]";
    }

    asort($names);

    return $names;
  }

  /**
   * Used to get the amount stored in this object.
   *
   * @param float
   * @param string Currency Code
   */
  public function getAmount()
  {
    return $this->amountValue;
  }

  /**
   * Used to set the amount stored in this object.
   *
   * @param float
   * @param string Currency Code
   * @return this
   */
  public function setAmount($amount, $currency = null)
  {
    $this->amountValue = floatval($amount);

    if ( $currency !== null )
    {
      $this->setCurrency($currency);
    }

    return $this;
  }

  /**
   * Provide the amount stored in this object rounded to the specified number
   * of decimal places.
   *
   * @param integer How many places to the right of the decimal
   * @return float
   */
  public function getRounded($digits = 0)
  {
    $digits = intval($digits);
    $rtn = round($this->amountValue, $digits);

    return $rtn;
  }

  /**
   * Will set the currency code stored in this object.  This class does not
   * handle currency conversion, so the amount will not be affected
   *
   * @param string Currency code
   * @return this
   */
  public function setCurrency($currencyCode)
  {
    $currency = strtoupper($currencyCode);

    if ( array_key_exists($currency, self::$currencyNames) )
    {
      $this->currencyCode = $currency;
    }

    return $this;
  }

  /**
   * Will provide the currency code presently in use.
   *
   * @param string Currency code
   * @param boolean
   */
  public function getCurrency()
  {
    return $this->currencyCode;
  }

  /**
   * Sets the precision of the output of this object.
   * Actual values stored and calculated always use the maximum amount of
   * precision that PHP allows for.
   *
   * @param integer
   * @return this
   */
  public function setPrecision($digits)
  {
    $this->precision = intval($digits);

    return $this;
  }

  /**
   * Provides what the precision setting is
   *
   * @return integer
   */
  public function getPrecision()
  {
    return $this->precision;
  }

  /**
   * Provide the monetary symbol for the specified currency, or for the
   * stored currency code.
   *
   * @param string
   * @return string
   */
  public static function getHtmlSymbol($currencyCode)
  {
    self::initHtmlSymbols();

    $sym = '';

    $currency = strtoupper($currencyCode);

    if ( array_key_exists($currency, self::$htmlSymbols) )
    {
      $sym = self::$htmlSymbols[$currency];
    }

    return $sym;
  }

  /**
   * Provides a formatted version of the amount stored in this object ready
   * for display.
   *
   * I really do intend to work on a more elegant solution here, but for now
   * I'm just going to manually plug in the formats that I'm aware of.
   *
   * @param float The amount to format
   * @return string The formatted amount
   */
  public function formattedHtmlAmount($amount)
  {
    $rtn = '';

    $cc = $this->currencyCode;

    $symbol = self::getHtmlSymbol($cc);

    switch ($cc)
    {
      case 'USD': // United States Dollar
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'EUR': // Euro
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'GBP': // United Kingdom Pounds
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'LBP': // Lebanon Pounds
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'JEP': // Jersey Pounds
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'EGP': // Egypt Pounds
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'MXN': // Mexican Pesos
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'CAD': // Canada Dollars
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'HKD': // Hong Kong Dollars
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'JPY': // Japan Yen
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'CNY': // China Yuan Renminbi
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'IDR': // Indonesia Rupiahs
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'HUF': // Hungary Forint
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'NOK': // Norway Kroner
        $rtn .= number_format($amount, $this->precision);
        $rtn .= '&nbsp;'.$symbol;
        break;

      case 'ISK': // Iceland Kronur
        $rtn .= number_format($amount, $this->precision);
        $rtn .= '&nbsp;'.$symbol;
        break;

      case 'PHP': // Philippines Pesos
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'ILS': // Israel New Shekels
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'KPW': // North Korea Won
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'KRW': // South Korea Won
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'QAR': // Qatar Riyals
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'YER': // Yemen Rials
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'SAR': // Saudi Arabia Riyals
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'IRR': // Iran Rials
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'TWD': // Taiwan New Dollars
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      case 'UAH': // Ukraine Hryvnia
        $rtn .= $symbol.'&nbsp;';
        $rtn .= number_format($amount, $this->precision);
        break;

      default: // Fall through for no special formatting known
        $rtn .= number_format($amount, $this->precision);
    }

    if ( $this->suffixFlag )
    {
      $rtn .= "&nbsp;".$cc;
    }

    return $rtn;
  }

  /**
   * Provide a format string suitable for setting up the number format in a
   * spreadsheet application.
   *
   * @param string
   * @return string
   */
  public static function getSpreadSheetFormat($currencyCode)
  {
    $cc = strtoupper(substr($currencyCode,0, 3));

    self::initSpreadSheetFormats();

    if ( array_key_exists($cc, self::$spreadSheetFormats) )
    {
      return self::$spreadSheetFormats[$cc];
    }
    else
    {
      return '#,##0.00 [$'.$cc.'];[RED]-#,##0.00 [$'.$cc.']';
    }
  }

  /**
   * Checks to see if the provided currency code is supported by this class
   *
   * @param string
   * @return boolean
   */
  public static function isCurrency($currencyCode)
  {
    $rtn = false;
    self::initCurrencies();

    if ( array_key_exists($currencyCode, self::$currencyNames) )
    {
      $rtn = true;
    }

    return $rtn;
  }


  /**
   * Loads up all the exchange rates and currency types from the
   * currency.xml file.
   */
  private static function initCurrencies()
  {
    if ( is_array(self::$currencyNames) )
    {
      if ( count(self::$currencyNames) > 0 ) {
        return;  // Only need to load these once.
      }
    }

    self::$currencyNames = array(
      'AED' => 'United Arab Emirates Dirhams',
      'AFN' => 'Afghanistan Afghanis',
      'ALL' => 'Albania Leke',
      'AMD' => 'Armenia Drams',
      'ANG' => 'Netherlands Antilles Guilders',
      'AOA' => 'Angola Kwanza',
      'ARS' => 'Argentina Pesos',
      'AUD' => 'Australia Dollars',
      'AWG' => 'Aruba Guilders',
      'AZN' => 'Azerbaijan New Manats',
      'BBD' => 'Barbados Dollars',
      'BDT' => 'Bangladesh Taka',
      'BGN' => 'Bulgaria Leva',
      'BHD' => 'Bahrain Dinars',
      'BIF' => 'Burundi Francs',
      'BMD' => 'Bermuda Dollars',
      'BND' => 'Brunei Dollars',
      'BOB' => 'Bolivia Bolivianos',
      'BRL' => 'Brazil Reais',
      'BSD' => 'Bahamas Dollars',
      'BTN' => 'Bhutan Ngultrum',
      'BWP' => 'Botswana Pulas',
      'BYR' => 'Belarus Rubles',
      'BZD' => 'Belize Dollars',
      'CAD' => 'Canada Dollars',
      'CDF' => 'Congo/Kinshasa Francs',
      'CHF' => 'Switzerland Francs',
      'CLP' => 'Chile Pesos',
      'CNY' => 'China Yuan Renminbi',
      'COP' => 'Colombia Pesos',
      'CRC' => 'Costa Rica Colones',
      'CUC' => 'Cuba Convertible Pesos',
      'CUP' => 'Cuba Pesos',
      'CVE' => 'Cape Verde Escudos',
      'CZK' => 'Czech Republic Koruny',
      'DJF' => 'Djibouti Francs',
      'DKK' => 'Denmark Kroner',
      'DOP' => 'Dominican Republic Pesos',
      'DZD' => 'Algeria Dinars',
      'EEK' => 'Estonia Krooni',
      'EGP' => 'Egypt Pounds',
      'ERN' => 'Eritrea Nakfa',
      'ETB' => 'Ethiopia Birr',
      'EUR' => 'Euro',
      'FJD' => 'Fiji Dollars',
      'FKP' => 'Falkland Islands Pounds',
      'GBP' => 'United Kingdom Pounds',
      'GEL' => 'Georgia Lari',
      'GGP' => 'Guernsey Pounds',
      'GHS' => 'Ghana Cedis',
      'GIP' => 'Gibraltar Pounds',
      'GMD' => 'Gambia Dalasi',
      'GNF' => 'Guinea Francs',
      'GTQ' => 'Guatemala Quetzales',
      'GYD' => 'Guyana Dollars',
      'HKD' => 'Hong Kong Dollars',
      'HNL' => 'Honduras Lempiras',
      'HRK' => 'Croatia Kuna',
      'HTG' => 'Haiti Gourdes',
      'HUF' => 'Hungary Forint',
      'IDR' => 'Indonesia Rupiahs',
      'ILS' => 'Israel New Shekels',
      'IMP' => 'Isle of Man Pounds',
      'INR' => 'India Rupees',
      'IQD' => 'Iraq Dinars',
      'IRR' => 'Iran Rials',
      'ISK' => 'Iceland Kronur',
      'JEP' => 'Jersey Pounds',
      'JMD' => 'Jamaica Dollars',
      'JOD' => 'Jordan Dinars',
      'JPY' => 'Japan Yen',
      'KES' => 'Kenya Shillings',
      'KGS' => 'Kyrgyzstan Soms',
      'KHR' => 'Cambodia Riels',
      'KMF' => 'Comoros Francs',
      'KPW' => 'North Korea Won',
      'KRW' => 'South Korea Won',
      'KWD' => 'Kuwait Dinars',
      'KYD' => 'Cayman Islands Dollars',
      'KZT' => 'Kazakhstan Tenge',
      'LAK' => 'Laos Kips',
      'LBP' => 'Lebanon Pounds',
      'LKR' => 'Sri Lanka Rupees',
      'LRD' => 'Liberia Dollars',
      'LSL' => 'Lesotho Maloti',
      'LTL' => 'Lithuania Litai',
      'LVL' => 'Latvia Lati',
      'LYD' => 'Libya Dinars',
      'MAD' => 'Morocco Dirhams',
      'MDL' => 'Moldova Lei',
      'MGA' => 'Madagascar Ariary',
      'MKD' => 'Macedonia Denars',
      'MMK' => 'Myanmar Kyats',
      'MNT' => 'Mongolia Tugriks',
      'MOP' => 'Macau Patacas',
      'MRO' => 'Mauritania Ouguiyas',
      'MUR' => 'Mauritius Rupees',
      'MVR' => 'Maldives Rufiyaa',
      'MWK' => 'Malawi Kwachas',
      'MXN' => 'Mexico Pesos',
      'MYR' => 'Malaysia Ringgits',
      'MZN' => 'Mozambique Meticais',
      'NAD' => 'Namibia Dollars',
      'NGN' => 'Nigeria Nairas',
      'NIO' => 'Nicaragua Cordobas',
      'NOK' => 'Norway Kroner',
      'NPR' => 'Nepal Rupees',
      'NZD' => 'New Zealand Dollars',
      'OMR' => 'Oman Rials',
      'PAB' => 'Panama Balboas',
      'PEN' => 'Peru Nuevos Soles',
      'PGK' => 'Papua New Guinea Kina',
      'PHP' => 'Philippines Pesos',
      'PKR' => 'Pakistan Rupees',
      'PLN' => 'Poland Zlotych',
      'PYG' => 'Paraguay Guarani',
      'QAR' => 'Qatar Riyals',
      'RON' => 'Romania New Lei',
      'RSD' => 'Serbia Dinars',
      'RUB' => 'Russia Rubles',
      'RWF' => 'Rwanda Francs',
      'SAR' => 'Saudi Arabia Riyals',
      'SBD' => 'Solomon Islands Dollars',
      'SCR' => 'Seychelles Rupees',
      'SDG' => 'Sudan Pounds',
      'SEK' => 'Sweden Kronor',
      'SGD' => 'Singapore Dollars',
      'SHP' => 'Saint Helena Pounds',
      'SLL' => 'Sierra Leone Leones',
      'SOS' => 'Somalia Shillings',
      'SPL' => 'Seborga Luigini',
      'SRD' => 'Suriname Dollars',
      'SVC' => 'El Salvador Colones',
      'SYP' => 'Syria Pounds',
      'SZL' => 'Swaziland Emalangeni',
      'THB' => 'Thailand Baht',
      'TJS' => 'Tajikistan Somoni',
      'TMM' => 'Turkmenistan Manats',
      'TMT' => 'Turkmenistan New Manats',
      'TND' => 'Tunisia Dinars',
      'TOP' => 'Tonga Pa\'anga',
      'TRY' => 'Turkey Lira',
      'TTD' => 'Trinidad and Tobago Dollars',
      'TVD' => 'Tuvalu Dollars',
      'TWD' => 'Taiwan New Dollars',
      'TZS' => 'Tanzania Shillings',
      'UAH' => 'Ukraine Hryvnia',
      'UGX' => 'Uganda Shillings',
      'USD' => 'United States Dollars',
      'UYU' => 'Uruguay Pesos',
      'UZS' => 'Uzbekistan Sums',
      'VEB' => 'Venezuela Bolivares',
      'VEF' => 'Venezuela Bolivares Fuertes',
      'VND' => 'Vietnam Dong',
      'VUV' => 'Vanuatu Vatu',
      'WST' => 'Samoa Tala',
      'XAF' => 'Communauté Financière Africaine Francs BEAC',
      'XAG' => 'Silver Ounces',
      'XAU' => 'Gold Ounces',
      'XCD' => 'East Caribbean Dollars',
      'XPD' => 'Palladium Ounces',
      'XPF' => 'Comptoirs Français du Pacifique Francs',
      'XPT' => 'Platinum Ounces',
      'YER' => 'Yemen Rials',
      'ZAR' => 'South Africa Rand',
      'ZMK' => 'Zambia Kwacha',
      'ZWD' => 'Zimbabwe Dollars'
    );
  }

  /**
   * Load up the array of currency symbols cross referenced to the currency
   * code.
   */
  private static function initHtmlSymbols()
  {
    if ( is_array(self::$htmlSymbols) )
    {
      if ( count(self::$htmlSymbols) > 0 )
      {
        return; // Already loaded
      }
    }

    self::$htmlSymbols = array
    (
      'USD' => '&#36;',
      'MXN' => '&#36;',
      'CAD' => '&#36;',
      'HKD' => '&#36;',
      'AUD' => '&#36;',
      'EUR' => '&euro;',
      'GBP' => '&pound;',
      'LBP' => '&pound;',
      'JEP' => '&pound;',
      'EGP' => '&pound;',
      'JPY' => '&yen;',
      'CNY' => '&yen;',
      'IDR' => 'Rp',
      'HUF' => 'Ft',
      'ISK' => 'kr',
      'NOK' => 'kr',
      'PHP' => 'Php',
      'ILS' => '&#8362;',
      'KPW' => '&#8361;',
      'KRW' => '&#8361;',
      'QAR' => '&#65020;',
      'YER' => '&#65020;',
      'SAR' => '&#65020;',
      'IRR' => '&#65020;',
      'TWD' => 'NT&#36;',
      'UAH' => '&#8372;'
    );
  }

  /**
   * This doesn't really do much yet... some day it might just!
   */
  private static function initSpreadSheetFormats()
  {
    if ( count(self::$spreadSheetFormats) > 0 )
    {
      return; // Already loaded.
    }

    self::$spreadSheetFormats = array
    (
      'USD' => '[$$]#,##0.00 [$USD];[RED]-[$$]#,##0.00 [$USD]',
      'CAD' => '[$$]#,##0.00 [$CAD];[RED]-[$$]#,##0.00 [$CAD]',
      'HKD' => '[$$]#,##0.00 [$HKD];[RED]-[$$]#,##0.00 [$HKD]',
      'MXN' => '[$$]#,##0.00 [$MXN];[RED]-[$$]#,##0.00 [$MXN]',
      'AUD' => '[$$]#,##0.00 [$AUD];[RED]-[$$]#,##0.00 [$AUD]',
      'EUR' => '[$€]#,##0.00 [$EUR];[RED]-[$€]#,##0.00 [$EUR]',
      'GBP' => '[$£]#,##0.00 [$GBP];[RED]-[$£]#,##0.00 [$GBP]',
      'LBP' => '[$£]#,##0.00 [$LBP];[RED]-[$£]#,##0.00 [$LBP]',
      'JEP' => '[$£]#,##0.00 [$JEP];[RED]-[$£]#,##0.00 [$JEP]',
      'EGP' => '[$£]#,##0.00 [$EGP];[RED]-[$£]#,##0.00 [$EGP]',
      'NOK' => '#,##0.00[$kr] [$NOK];[RED]-#,##0.00[$kr] [$NOK]',
      'ISK' => '#,##0.00[$kr] [$ISK];[RED]-#,##0.00[$kr] [$ISK]',
      'JPY' => '[$￥]#,##0 [$JPY];[RED]-[$￥]#,##0 [$JPY]',
      'CNY' => '[$￥]#,###.## [$CNY];[RED]-[$￥]#,###.## [$CNY]'
    );
  }
}
