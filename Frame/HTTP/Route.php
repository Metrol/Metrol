<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Frame\HTTP;

/**
 * Extends the Route class to include HTTP specific information
 */
class Route extends \Metrol\Frame\Route
{
  /**
   * The string that will be compared against the incoming URI for a match
   *
   * @var string
   */
  protected $match;

  /**
   * The segments of the $match string broken apart by the slashes
   *
   * @var array
   */
  protected $segments;

  /**
   * The HTTP method used for this request
   *
   * @var string
   */
  protected $method;

  /**
   * The HTTP status code to look for
   *
   * @var integer
   */
  protected $status;

  /**
   * Defines how many segments after the last filter segment will be allowed
   * as arguments to the action.
   *
   * @var integer
   */
  protected $maxParams;

  /**
   * The user may specify text to populate an anchors title attribute.  If so,
   * it will be stored here.
   *
   * @var string
   */
  protected $tagTitle;

  /**
   * Sets the page title of the location this route points to.  Entirely
   * optional.
   *
   * @var string
   */
  protected $pageTitle;

  /**
   * As parameters are found in segments or hints they are loaded into this
   * array.  If it is found that the route matches, these values will be
   * loaded into the parent arguments listing.
   *
   * @var array
   */
  protected $foundArgs;

  /**
   * Initilizes the Route object
   *
   * @param string Name of the route
   */
  public function __construct($routeName)
  {
    parent::__construct($routeName);

    $this->match            = '';
    $this->segments         = array();
    $this->originalSegments = array();
    $this->maxParams        = 0;
    $this->method           = 'GET';
    $this->status           = 200;
    $this->foundArgs        = array();
    $this->tagTitle         = '';
    $this->pageTitle        = '';
  }

  /**
   * Diagnostic output extension from the parent class adding the extra HTTP
   * stuff we've got here.
   *
   * @return string
   */
  public function __toString()
  {
    $rtn = parent::__toString();

    $rtn .= "       Match: ";
    $rtn .= htmlentities($this->match)."\n";
    $rtn .= " HTTP Method: ".$this->method."\n";
    $rtn .= " HTTP Status: ".$this->status."\n";
    $rtn .= '  Max Params: '.$this->maxParams."\n";
    $rtn .= " Segments to Match: ".print_r($this->segments, true);
    $rtn .= " Original Segments: ".print_r($this->originalSegments, true);

    return $rtn;
  }

  /**
   * Sets the status code to be looking for
   *
   * @param integer
   */
  public function setStatus($httpStatusCode)
  {
    $this->status = intval($httpStatusCode);
  }

  /**
   * Sets the URI filtering string
   *
   * @param string
   */
  public function setMatch($match)
  {
    $this->match = $match;

    if ( strpos($match, '/') !== FALSE )
    {
      $parts = $this->explodeURI($match);

      foreach ( $parts as $segment )
      {
        if ( strlen($segment) > 0 )
        {
          $this->segments[] = strtolower($segment);
        }
      }
    }
  }

  /**
   * Sets the maximum number of segments following the match filter that will
   * be allowed to exist, and pass on to the action as arguments
   *
   * @param integer
   */
  public function setMaxParameters($maxParameters)
  {
    $this->maxParams = intval($maxParameters);
  }

  /**
   * Sets the HTTP request method
   *
   * @param string GET|POST
   */
  public function setMethod($method)
  {
    $m = strtoupper($method);

    if ( $m == 'GET' )
    {
      $this->method = $m;
    }

    if ( $m == 'POST' )
    {
      $this->method = $m;
    }
  }

  /**
   * Sets the text that can be used for the anchor tag title attribute
   *
   * @param string
   */
  public function setTagTitle($title)
  {
    $this->tagTitle = $title;
  }

  /**
   * Sets the page title of the location this route points to
   *
   * @param string
   */
  public function setPageTitle($title)
  {
    $this->pageTitle = $title;
  }

  /**
   * Provide the tag title that has been set here
   *
   * @return string
   */
  public function getTagTitle()
  {
    return $this->tagTitle;
  }

  /**
   * Provide the page title that was set in here
   *
   * @return string
   */
  public function getPageTitle()
  {
    return $this->pageTitle;
  }

  /**
   * Assembles a URL based on the arguments and filtering information
   *
   * @return string
   */
  public function getURL()
  {
    $rtn = '/';

    if ( count($this->segments) == 0 and count($this->arguments) == 0 )
    {
      return $rtn;
    }

    $args = $this->arguments;

    foreach ( $this->segments as $seg )
    {
      if ( strpos($seg, ':') !== false )
      {
        if ( count($args) > 0 )
        {
          $rtn .= urlencode(array_shift($args));
        }
        else
        {
          $rtn .= 'unk';
        }
      }
      else
      {
        $rtn .= $seg;
      }

      $rtn .= '/';
    }

    $segCount = 0;

    while ( count($args) > 0 and $segCount < $this->maxParams )
    {
      $rtn .= urlencode(array_shift($args));
      $rtn .= '/';
      $segCount++;
    }

    return $rtn;
  }

  /**
   * Check to see if this route is a match for the specified HTTP request
   *
   * @param \Metrol\Frame\HTTP\Request
   * @return boolean
   */
  public function checkRequestMatch(Request $req)
  {
    if ( $req->server->status != $this->status )
    {
      return false;
    }

    if ( $req->server->method != strtoupper($this->method) )
    {
      return false;
    }

    if ( strlen($this->match) == 0 )
    {
      return false;
    }

    $testURI = strtolower($req->server->uri);

    // Should be one slash no matter what
    if ( strpos($testURI, '/') === false )
    {
      // print "FAIL Match, no slash in the URI<br />\n";
      return false;
    }

    // Strip any GET query information at the end of the
    if ( strpos($testURI, '?') !== false )
    {
      $testURI = substr($testURI, 0, strpos($testURI, '?'));
    }

    // Break up the URI into segments that can be compared to the segments from
    // the route loader.
    $uriSegments = $this->explodeURI($testURI);

    $segCount    = count($this->segments);
    $uriSegCount = count($uriSegments);

    // Be sure to handle a doc root request properly
    if ( $uriSegCount == 0 and $this->match == '/' )
    {
      return true;
    }
    elseif ( $uriSegCount == 0 and $this->match != '/' )
    {
      // print "FAIL Match, trying to resolve doc root<br />\n";
      return false;
    }

    // Need to at LEAST have as many segments in the URI as in the match filter
    if ( $uriSegCount < $segCount )
    {
      // print "FAIL Match, Not enough segments in the URI<br />\n";
      return false;
    }

    // The URI segments can't exceed the number of segments in the filter plus
    // the allowed parameters.
    if ( $uriSegCount > $segCount + $this->maxParams )
    {
      // print "FAIL Match, Too many parameter segments<br />\n";
      return false;
    }

    // Okay, so now walk through each segment looking to see that each one
    // matches.
    $rtn = $this->matchSegments($uriSegments);

    // Check for additional segments in the URI, as they will be arguments to
    // the controller
    if ( $rtn and $uriSegCount > $segCount )
    {
      $origSegments = $this->explodeURI($req->server->uri);

      for ( $i = $segCount; $i < $uriSegCount; $i++ )
      {
        $this->foundArgs[] = $origSegments[$i];
      }
    }

    // Apply any found arguments to the main list of arguments if this is a
    // match.
    if ( $rtn and count($this->foundArgs) > 0 )
    {
      foreach ( $this->foundArgs as $arg )
      {
        $this->addArguments($arg);
      }
    }

    return $rtn;
  }

  /**
   * Breaks apart the input URI into an array of segments
   *
   * @param string URI
   * @return array
   */
  protected function explodeURI($uri)
  {
    $uriSegments = array();
    $uriParts = explode('/', $uri);

    foreach ( $uriParts as $uriSegment )
    {
      if ( strlen($uriSegment) > 0 )
      {
        $uriSegments[] = $uriSegment;
      }
    }

    return $uriSegments;
  }

  /**
   * Walk through all the segments looking to see if things are matching up
   *
   * @param array Segments from URI
   * @return boolean TRUE if all matched
   */
  protected function matchSegments(array $uriSegments)
  {
    foreach ( $this->segments as $segIdx => $segment )
    {
      if ( strpos($segment, ':') !== false )
      {
        if ( $this->hintMatch($segment, $uriSegments[$segIdx]) == false )
        {
          return false;
        }

        continue;
      }

      // Fall through to a Literal match if no special characters
      if ( $this->literalMatch($segment, $uriSegments[$segIdx]) == false )
      {
        return false;
      }
    }

    return true;
  }

  /**
   * Compares the filter segment to a literal looking for a match
   *
   * @param string Filter segment
   * @param string URI Request segment
   * @return boolean
   */
  protected function literalMatch($segment, $uriSegment)
  {
    $rtn = false;

    if ( $segment == $uriSegment )
    {
      $rtn = true;
    }

    return $rtn;
  }

  /**
   * Compares the URI segment to a type hinted filter
   *
   * @param string Filter segment
   * @param string URI Request segment
   * @return boolean
   */
  protected function hintMatch($segment, $uriSegment)
  {
    $rtn = false;

    switch (substr($segment, 0, 4))
    {
      case ':int':
        $rtn = $this->compareInteger($segment, $uriSegment);
        break;

      case ':num':
        $rtn = $this->compareNumber($segment, $uriSegment);
        break;

      case ':str':
        $rtn = $this->compareString($segment, $uriSegment);
        break;

      default:
        return false;
    }

    if ( $rtn )
    {
      $this->foundArgs[] = $uriSegment;
    }

    return $rtn;
  }

  /**
   * Used to test a numeric hint match
   *
   * @param string Filter segment
   * @param string URI Request segment
   * @return boolean
   */
  protected function compareNumber($segment, $uriSegment)
  {
    $rtn = false;

    if ( is_numeric($uriSegment) )
    {
      $rtn = true;
    }

    if ( !$rtn )
    {
      return false;
    }

    // If there's only 4 chars, then it's just the type hint
    if ( strlen($segment) == 4 )
    {
      return $rtn;
    }

    $specSect  = substr($segment, 4);

    if ( substr($specSect, 0, 1) == '[' and substr($specSect, -1) == ']' )
    {
      $specs = substr($specSect, 1, -1);

      if ( strpos($specs, '-') === false )
      {
        if ( $uriSegment == $specs )
        {
          return true;
        }
        else
        {
          return false;
        }
      }
      else
      {
        $specParts = explode('-', $specs);
        $min = $specParts[0];
        $max = $specParts[1];

        if ( strlen($min) > 0 and floatval($uriSegment) < floatval($min) )
        {
          return false;
        }

        if ( strlen($max) > 0 and floatval($uriSegment) > floatval($max) )
        {
          return false;
        }

        return true;
      }
    }

    return false;
  }

  /**
   * Used to test an integer hint match
   *
   * @param string Filter segment
   * @param string URI Request segment
   * @return boolean
   */
  protected function compareInteger($segment, $uriSegment)
  {
    $rtn = false;

    if ( is_numeric($uriSegment) )
    {
      if ( $uriSegment == intval($uriSegment) )
      {
        $rtn = true;
      }
    }

    if ( !$rtn )
    {
      return false;
    }

    $rtn = $this->compareNumber($segment, $uriSegment);

    return $rtn;
  }

  /**
   * Used to test a string hint match
   *
   * @param string Filter segment
   * @param string URI Request segment
   * @return boolean
   */
  protected function compareString($segment, $uriSegment)
  {
    // If there's only 4 chars, then it's just the type hint
    if ( strlen($segment) == 4 )
    {
      return true;
    }

    $uriSegLen = strlen($uriSegment);
    $specSect  = substr($segment, 4);

    if ( substr($specSect, 0, 1) == '[' and substr($specSect, -1) == ']' )
    {
      $specs = substr($specSect, 1, -1);

      if ( strpos($specs, '-') === false )
      {
        if ( $uriSegLen == intval($specs) )
        {
          return true;
        }
        else
        {
          return false;
        }
      }
      else
      {
        $specParts = explode('-', $specs);
        $min = $specParts[0];
        $max = $specParts[1];

        if ( strlen($min) > 0 and $uriSegLen < intval($min) )
        {
          return false;
        }

        if ( strlen($max) > 0 and $uriSegLen > intval($max) )
        {
          return false;
        }

        return true;
      }
    }

    return true;
  }
}
