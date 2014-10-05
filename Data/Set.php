<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Data;

/**
 * Maintains a list of Data Items
 */
class Set implements \Iterator, \Countable, \JsonSerializable
{
  /**
   * List of Data Items
   *
   * @var array
   */
  protected $dataSet;

  /**
   * Objects and information related to the main data set, indexed by the main
   * data set's index.
   *
   * @var array
   */
  protected $relatedSet;

  /**
   * When set, this list is restricted to a data item of this type
   *
   * @param string
   */
  protected $itemType;

  /**
   * Initilizes the Data Set
   */
  public function __construct()
  {
    $this->dataSet    = array();
    $this->relatedSet = array();

    $this->itemType = "\Metrol\Data\Item";
  }

  /**
   * Quick diagnostic output of what all has been slapped in here.
   *
   * @return string
   */
  public function __toString()
  {
    $rtn  = 'Data Set: <tt>'.get_class($this)."</tt><br />\n";
    $rtn .= "Contains a set of <tt>".$this->itemType."</tt> item objects<hr />\n";

    foreach ( $this as $key => $val )
    {
      if ( is_object($val) )
      {
        if ( method_exists($val, '__toString') )
        {
          $rtn .= $key.' = '.$val."<br />\n";
        }
        else
        {
          $rtn .= $key.' = '.get_class($val)." Object<br />\n";
        }
      }
      else
      {
        $rtn .= $key." = ".$val."<br />\n";
      }
    }

    return $rtn;
  }

  /**
   * Provide a JSON ready array of all the values that have been stored here.
   *
   * @return array
   */
  public function jsonSerialize()
  {
    $rtn = array();

    foreach ( $this as $idx => $item )
    {
      foreach ( $item as $field => $value )
      {
        $rtn[$idx][$field] = $value;
      }
    }

    foreach ( $this->relatedSet as $relateType => $relSet )
    {
      foreach ( $relSet as $relIdx => $relItem )
      {
        if ( array_key_exists($relIdx, $rtn) )
        {
          $rtn[$relIdx][$relateType] = $relItem;
        }
      }
    }

    return $rtn;
  }

  /**
   * Dump out the dataset for troubleshooting
   *
   */
  public function dump()
  {
    var_dump($this->dataSet);
  }

  /**
   * Sets the kind of data item this list is allowed to accept.
   *
   * @param \Metrol\Data\Item
   * @return this
   */
  protected function setItemType(Item $item)
  {
    $this->itemType = get_class($item);
  }

  /**
   * Remove a single item from the list
   *
   * @param string|integer
   * @return this
   */
  public function removeItem($index)
  {
    if (array_key_exists($index, $this->dataSet) )
    {
      unset($this->dataSet[$index]);
    }

    foreach ( $this->relatedSet as $relateType => $relSet )
    {
      if ( array_key_exists($index, $relSet) )
      {
        unset($this->relatedSet[$relateType][$index]);
        continue;
      }
    }

    return $this;
  }

  /**
   * Empties the data set back to an initial state
   *
   * @return this
   */
  public function clearAll()
  {
    $this->dataSet    = array();
    $this->relatedSet = array();

    return $this;
  }

  /**
   * Provide an empty Data\Item
   *
   * @return \Metrol\Data\Item
   */
  public function emptyItem()
  {
    $rtn = new $this->itemType;

    return $rtn;
  }

  /**
   * Adds a Data Item to the stack
   *
   * @param \Metrol\Data\Item
   * @param mixed Optional index value
   * @return this
   */
  public function add(Item $item, $index = null)
  {
    if ( ! $item instanceOf $this->itemType )
    {
      return $this;
    }

    if ( $index === null )
    {
      $this->dataSet[] = $item;
    }
    else
    {
      $this->dataSet[$index] = $item;
    }

    return $this;
  }

  /**
   * Allows a caller to attach additional objects and information to an existing
   * data item.  The kind of related data is specified by name, then indexed by
   * the same index as the primary data set.
   *
   * NOTE: If the specified index does not exist, the related data will not be
   *       added.  There must be something to relate to.
   *
   * @param string Name of the related type
   * @param mixed  Index value of an existing data record
   * @param mixed  The data to relate
   * @return this
   */
  public function addRelated($relateType, $index, $dataItem)
  {
    if ( $this->indexInSet($index) )
    {
      $this->relatedSet[$relateType][$index] = $dataItem;
    }

    return $this;
  }

  /**
   * Takes in an entire data set and combines it with the primary set as
   * related data.
   *
   * @param string Name of the related type
   * @param \Metrol\Data\Set
   * @param string Field name to link to the primary list index
   * @return this
   */
  public function addRelatedSet($relateType, \Metrol\Data\Set $relSet, $linkField)
  {
    foreach ( $relSet as $relItem )
    {
      $index = $relItem->$linkField;
      $this->addRelated($relateType, $index, $relItem);
    }

    return $this;
  }

  /**
   * Retrieves a single item from set of data.
   * When no index is specified, the current data pointer will be used and then
   * incremented.
   * Without any matching data, an empty object will be returned
   *
   * @param mixed Index value of the data
   * @return \Metrol\Data\Item
   */
  public function fetch($index = null)
  {
    $rtn = null;

    if ( $index === null )
    {
      $rtn = $this->current();
      $this->next();
    }
    elseif (array_key_exists($index, $this->dataSet) )
    {
      $rtn = $this->dataSet[$index];
    }

    // Make sure we've got an object
    if ( !is_object($rtn) )
    {
      $rtn = $this->emptyItem();
    }

    return $rtn;
  }

  /**
   * Fetches a related data item based on the type of data and the index it is
   * stored under
   *
   * @param string Name of the related data
   * @param mixed  Index value
   * @return mixed|null The related data found, or null if nothing is there.
   */
  public function fetchRelated($relateType, $index)
  {
    $rtn = null;

    if ( array_key_exists($relateType, $this->relatedSet) )
    {
      if ( array_key_exists($index, $this->relatedSet[$relateType]) )
      {
        $rtn = $this->relatedSet[$relateType][$index];
      }
    }

    return $rtn;
  }

  /**
   * Rewinds the data pointer and returns the top most item on the list.
   * Returns an empty item object if the list is still empty.
   *
   * @return \Metrol\Data\Item
   */
  public function fetchTop()
  {
    $this->rewind();

    return $this->current();
  }

  /**
   * Provides a list of all the key index values that are in this set
   *
   * @return array
   */
  public function getKeyValues()
  {
    return array_keys($this->dataSet);
  }

  /**
   * Determines if a field with the specified value exists in the set or not
   *
   * @param string Field Name
   * @param mixed Value to look for
   * @return boolean
   */
  public function inSet($field, $value)
  {
    $rtn = false;

    foreach ( $this as $item )
    {
      if ( $item->getValue($field) == $value )
      {
        $rtn = true;
        break;
      }
    }

    return $rtn;
  }

  /**
   * Searches through the set of data to determine if any records with the
   * specified index exist.
   *
   * @param mixed
   * @return boolean
   */
  public function indexInSet($index)
  {
    $rtn = false;

    if ( array_key_exists($index, $this->dataSet) )
    {
      $rtn = true;
    }

    return $rtn;
  }

  /**
   * Walks through all of the values in the list looking for the specified
   * key matching a value.
   * Provided back is an array of the found items.
   *
   * @param string The field to search
   * @param mixed The value to look for
   * @return array List of Item objects
   */
  public function findMatches($key, $searchFor)
  {
    $rtn = array();

    foreach ( $this as $index => $item )
    {
      if ( $item->$key == $searchFor )
      {
        $rtn[$index] = $item;
      }
    }

    return $rtn;
  }

  /**
   * Walks through all of the values in the list looking for the specified
   * key matching a value.
   * Provided back is the first match found.  The search is terminated at that
   * point.
   *
   * @param string The field to search
   * @param mixed The value to look for
   * @return \Metrol\Data\Item
   */
  public function findMatch($key, $searchFor)
  {
    $rtn = new \Metrol\Data\Item;

    foreach ( $this as $item )
    {
      if ( $item->$key == $searchFor )
      {
        $rtn = $item;
        break;
      }
    }

    return $rtn;
  }

  /**
   * Checks to see if any data is in this set.
   *
   * @return boolean
   */
  public function hasData()
  {
    if ( $this->count() > 0 )
    {
      return true;
    }
    else
    {
      return false;
    }
  }

  /**
   * Provides a list of all the values for a given field name
   *
   * @param string Name of the field
   * @param string Name of the field to use as the array index
   * @return array
   */
  public function getFieldValues($fieldName, $keyField = null)
  {
    $rtn = array();

    foreach ( $this->dataSet as $idx => $item )
    {
      if ( $keyField === null )
      {
        $rtn[$idx] = $item->$fieldName;
      }
      else
      {
        $rtn[$item->$keyField] = $item->$fieldName;
      }
    }

    return $rtn;
  }

  /**
   * Provides a list of the unique values for a given field name
   *
   * @param string Name of the field
   * @return array
   */
  public function getUniqueFieldValues($fieldName)
  {
    $rtn = array();

    foreach ( $this->dataSet as $item )
    {
      if ( !in_array($item->$fieldName, $rtn) )
      {
        $rtn[] = $item->$fieldName;
      }
    }

    return $rtn;
  }

  /**
   * Sorts the data set based on the specified field in the items.
   *
   * @param string Field name to sort on
   * @param string Asc|Desc
   * @return this
   */
  public function sort($fieldName, $direction = 'Asc')
  {
    if ( $this->count() == 0 )
    {
      return $this;
    }

    $origSet    = $this->dataSet;
    $sortValues = $this->getFieldValues($fieldName);

    if ( strtolower($direction) === 'asc' )
    {
      asort($sortValues);
    }
    else
    {
      arsort($sortValues);
    }

    $this->dataSet = array();
    $keys = array_keys($sortValues);

    foreach ( $keys as $idx )
    {
      $this->dataSet[$idx] = $origSet[$idx];
    }

    return $this;
  }

  /**
   * Report how many items we've got in here
   *
   * @return integer
   */
  public function count()
  {
    return count($this->dataSet);
  }

  /**
   * Implementing the Iterartor interface to walk through the data items
   */
  public function rewind()
  {
    reset($this->dataSet);
  }

  public function current()
  {
    return current($this->dataSet);
  }

  public function key()
  {
    return key($this->dataSet);
  }

  public function next()
  {
    return next($this->dataSet);
  }

  public function valid()
  {
    return $this->current() !== false;
  }
}
