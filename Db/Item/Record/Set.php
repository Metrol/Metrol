<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Item\Record;

/**
 * Defines a set of Record objects
 */
class Set extends \Metrol\Db\Item\Set
{
  /**
   * Initialize the Set object
   *
   * @param \Metrol\Db\Item\Record
   */
  public function __construct(\Metrol\Db\Item\Record $obj)
  {
    parent::__construct($obj);
  }

  /**
   * Execute the query and store the results into the dataSet member
   *
   * @return this
   */
  public function run($key = null)
  {
    if ( $key === null )
    {
      $key = $this->source->primaryKey;
    }

    parent::run($key);

    return $this;
  }

  /**
   * Execute the query and return an array of primary keys only.
   * No objects are created or stored here.
   *
   * @return array
   */
  public function runForIndexes()
  {
    return parent::runForField($this->source->primaryKey);
  }

  /**
   * Removes all the items from the database and this list
   *
   * @return this
   */
  public function deleteAll()
  {
    foreach ( $this as $obj )
    {
      $obj->delete();
    }

    return $this;
  }

  /**
   * Adds a new Data Record to the set.
   *
   * @param \Metrol\Data\Item
   * @param mixed Optional index value
   * @return this
   */
  public function add(\Metrol\Data\Item $item, $index = null)
  {
    if ( $index === null)
    {
      $index = $item->id;
    }

    parent::add($item, $index);

    return $this;
  }
}
