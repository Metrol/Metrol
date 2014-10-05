<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Text;

/**
 * Provides basic encryption/decryptions tools
 */
class Encryption
{
  /**
   * The encryption type to be used by mcrypt
   *
   * @const
   */
  const ENCRYPT_TYPE = "tripledes";

  /**
   * Set the mcrypt mode
   *
   * @const
   */
  const MCRYPT_MODE  = "ecb";

  /**
   * The key to be used to lock/unlock strings
   *
   * @var string
   */
  private $key;

  /**
   * Sets the encryption key used for encoding and decoding
   *
   * @param string
   */
  public function __construct($key = "")
  {
    $this->setKey($key);
  }

  /**
   * Sets the key to be used for locking/unlocking data
   *
   * @param string
   */
  public function setKey($key)
  {
    $this->key = $key;
  }

  /**
   * Returns a random string of characters [0-9][a-z][A-Z] of the specified
   * length.  This can be a time expensive operation, so use sparingly.
   *
   * @param integer How many characters should be generated
   * @return string Random characters
   */
  public function genRandomString($length = 20)
  {
    $bigFatPrime = 15450763;
    $validChars  = array();
    $charCnt     = 0;
    $randString  = "";

    // Though not required anymore, seed the randomizer
    list($usec, $sec) = explode(' ', microtime() );
    $seed = (float) $sec + ((float) $usec * $bigFatPrime);
    $seed = intval($seed * 100);
    mt_srand($seed);

    // Get together a list of valid characters to pull from
    // Numbers
    for ($i = 48; $i <= 57; $i++)
    {
      $validChars[] = $i;
      $charCnt++;
    }

    // Upper case letters
    for ($i = 65; $i <= 90; $i++)
    {
      $validChars[] = $i;
      $charCnt++;
    }

    // Lower case letters
    for ($i = 97; $i <= 122; $i++)
    {
      $validChars[] = $i;
      $charCnt++;
    }

    shuffle($validChars);  // Randomize the list a bit

    // Lower case letters
    for ($i = 1; $i <= $length; $i++)
    {
      $randPick = mt_rand(0, $charCnt - 1);
      $char = chr($validChars[$randPick]);
      $randString .= $char;
    }

    return $randString;
  }

  /**
   * Encodes the string passed in and returns the result. A key must be set
   * or this method will not run.
   *
   * @param string Some text you would like to have encrypted
   * @param string Should the output be URL encoded.  Handy for GET strings.
   * @return string The input text encrypted
   */
  public function encode($input, $urlEncode = FALSE)
  {
    if ( strlen($input) == 0 )
    {
      return;
    }

    // Without a key, go no further
    if ( strlen($this->key) < 5 )
    {
      return FALSE;
    }

    // Double encode the key before use
    $key = sha1(md5($this->key));

    // Open up the mcrypt session
    $td = mcrypt_module_open(self::ENCRYPT_TYPE, "", self::MCRYPT_MODE, "");
    $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

    // Make sure we've got the proper key length here
    $keySize = mcrypt_enc_get_key_size($td);
    $key     = substr($key, 0, $keySize);

    // Initialize mcrypt session and encrypt the string
    mcrypt_generic_init($td, $key, $iv);
    $encrypted_data = mcrypt_generic($td, $input);

    // Shut down the mcrypt session
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);

    // Base64 encode the data for easier database handling
    $rtn = base64_encode($encrypted_data);

    $this->encoded    = $rtn;
    $this->lastResult = $rtn;

    if ( $urlEncode )
    {
      $rtn = urlencode($rtn);
    }

    return $rtn;
  }

  /**
   * Decodes the string passed in using the instance key.
   *
   * @param string Previously encrypted text
   * @return string Decrypted text
   */
  public function decode($input)
  {
    if ( strlen($input) == 0 )
    {
      return "";
    }

    // Without a key, go no further
    if ( strlen($this->key) < 5 )
    {
      return FALSE;
    }

    // The input will be Base64 encoded, so we need to undo that
    $input = base64_decode($input);

    // Double encode the key before use
    $key = sha1(md5($this->key));

    // Open up the mcrypt session
    $td = mcrypt_module_open(self::ENCRYPT_TYPE, "", self::MCRYPT_MODE, "");
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);

    // Make sure we've got the proper key length here
    $keySize = mcrypt_enc_get_key_size($td);
    $key     = substr($key, 0, $keySize);

    // Initialize mcrypt session and decrypt the string
    mcrypt_generic_init($td, $key, $iv);
    $decrypted_data = mdecrypt_generic($td, $input);

    // Shut down the mcrypt session
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);

    // Trim up the naughty white space left behind by the decrypt
    $rtn = trim($decrypted_data);

    return $rtn;
  }
}