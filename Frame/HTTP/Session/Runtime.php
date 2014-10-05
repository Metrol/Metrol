<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTTP\Session;

/**
 * Provides an API to the various runtime tweaks that can be applied to a
 * session.
 */
class Runtime
{
  /**
   * Initialize the Runtime object
   *
   */
  public function __construct()
  {
    // Some reasonable defaults for all sessions
    $this->setRuntime('use_only_cookies', TRUE);     // Only cookie sessions
    $this->setRuntime('hash_function', 1);           // SHA1 session IDs
    $this->setRuntime('hash_bits_per_character', 6); // Lots oh bytes
  }

  /**
   * Sets the name of the session to something other than PHPSESSID
   *
   * @param string Session name
   * @return this
   */
  public function setName($name)
  {
    \session_name($name);

    return $this;
  }

  /**
   * Sets the file path where session data is stored.  Not applicable when
   * using a custom handler.
   *
   * @param string File path
   * @return this
   */
  public function setPath($path)
  {
    \session_save_path($path);

    return $this;
  }

  /**
   * Force the session to regenerate the ID, but without losing the data in
   * the session itself.
   *
   * @return this
   */
  public function regenID()
  {
    \session_regenerate_id();

    return $this;
  }

  /**
   * The cache limiter defines which cache control HTTP headers are sent to the
   * client. These headers determine the rules by which the page content may be
   * cached by the client and intermediate proxies. Setting the cache limiter
   * to nocache disallows any client/proxy caching. A value of public permits
   * caching by proxies and the client, whereas private disallows caching by
   * proxies and permits the client to cache the contents.
   *
   * In private mode, the Expire header sent to the client may cause confusion
   * for some browsers, including Mozilla. You can avoid this problem by using
   * private_no_expire mode. The Expire header is never sent to the client in
   * this mode.
   *
   * Setting the cache limiter to '' will turn off automatic sending of cache
   * headers entirely.
   *
   * @param string Cache limiter
   * @return this
   */
  public function setCacheLimiter($cache_limiter)
  {
    switch ($cache_limiter)
    {
      case 'public':
        \session_cache_limiter('public');
        break;

      case 'private_no_expire':
        \session_cache_limiter('private_no_expire');
        break;

      case 'private':
        \session_cache_limiter('private');
        break;

      case 'nocache':
        \session_cache_limiter('nocache');
        break;

      default:
        break;
    }

    return $this;
  }

  /**
   * Sets the time to expire for the cache
   *
   * @param integer Cache expire time in seconds
   * @return this
   */
  public function setCacheExpire($expireSeconds)
  {
    \session_cache_expire(intval($expireSeconds));

    return $this;
  }

  /**
   * Provides a very generic way to set any of the session runtime settings.
   * No error checking is done, so call at your own risk!
   *
   * @param string Name of setting
   * @param mixed  Value to assign
   * @return this
   */
  public function setRuntime($setting, $value)
  {
    \ini_set('session.'.$setting, $value);

    return $this;
  }
}
