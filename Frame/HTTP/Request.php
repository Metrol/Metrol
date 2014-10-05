<?php
/**
 * @author "Michael Collette" <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTTP;

/**
 * Collects all the HTTP request data and makes it available to objects that
 * need to put that data to use.
 */
class Request extends \Metrol\Frame\Request
{
  /**
   * Keeps a copy of the Session object passed into here
   *
   * @var Session
   */
  protected $session;

  /**
   * Initialize the Request
   */
  public function __construct()
  {
    parent::__construct();

    $this->session = null;
  }

  /**
   * Provides a way to manually push the Session object into here
   *
   * @param Session
   */
  public function setSession(Session $session)
  {
    $this->session = $session;
  }

  /**
   * Outputs the Request diagnostic output
   *
   * @return string
   */
  public function __toString()
  {
    $rtn = parent::__toString();

    $rtn .= '** REQUEST Values: (Access with "->request")'."\n";
    $rtn .= '-------------------------------------------------'."\n";
    $rtn .= $this->request."\n";

    $rtn .= '** GET Values: (Access with "->get")'."\n";
    $rtn .= '-------------------------------------------------'."\n";
    $rtn .= $this->get."\n";

    $rtn .= '** POST Values: (Access with "->post")'."\n";
    $rtn .= '-------------------------------------------------'."\n";
    $rtn .= $this->post."\n";

    $rtn .= '** COOKIE Values: (Access with "->cookie")'."\n";
    $rtn .= '-------------------------------------------------'."\n";
    $rtn .= $this->cookie."\n";

    $rtn .= '** File Values: (Access with "->files")'."\n";
    $rtn .= '-------------------------------------------------'."\n";
    $rtn .= $this->files."\n";

    $rtn .= '** Server Values: (Access with "->server")'."\n";
    $rtn .= '-------------------------------------------------'."\n";
    $rtn .= $this->server."\n";

    $rtn .= '** Everything in $_SESSION: (Access with "->session")'."\n";
    $rtn .= '-------------------------------------------------'."\n";
    $rtn .= $this->session."\n";

    $rtn .= '** All the User Defined Constants:'."\n";
    $rtn .= '-------------------------------------------------'."\n";
    $rtn .= "Disabled output until get_defined_constants() problem resolved\n";
    $rtn .= '  ---------------------------'."\n";
    // $constants = get_defined_constants(true);

    // foreach ( $constants['user'] as $key => $val )
    // {
      // $rtn .= "  | $key = $val\n";
    // }

    $rtn .= '  ---------------------------'."\n\n";

    $rtn .= '** Everything in $_SERVER:'."\n";
    $rtn .= '-------------------------------------------------'."\n";
    $rtn .= print_r($_SERVER, true)."\n";

    return $rtn;
  }

  /**
   * @param string Value key
   * @return mixed
   */
  public function __get($var)
  {
    $rtn = null;

    switch (strtolower($var))
    {
      case 'request':
        $rtn = new Request\Request;
        break;

      case 'get':
        $rtn = new Request\Get;
        break;

      case 'post':
        $rtn = new Request\Post;
        break;

      case 'cookie':
        $rtn = new Request\Cookie;
        break;

      case 'session':
        if ( is_object($this->session) )
        {
          $rtn = $this->session;
        }
        else
        {
          $rtn = null;
        }

        break;

      case 'files':
        $rtn = new Request\Files;
        break;

      case 'server':
        $rtn = new Request\Server;
        break;

      default:
        $rtn = parent::__get($var);
    }

    return $rtn;
  }
}
