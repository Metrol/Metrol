<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Item;

/**
 * Defines a set of objects coming from a database
 */
class Set extends \Metrol\Data\Set
{
  /**
   * Keep a copy of the DB Source this record set is from
   *
   * @var \Metrol\Db\Source
   */
  protected $source;

  /**
   * The SQL engine used to create the query that will create the set of data
   *
   * @var \Metrol\Db\SQL
   */
  protected $sqlEngine;

  /**
   * Flag is enabled any time one of the run methods are called.  It's then
   * up to child classes to determine how best to use this flag
   *
   * @var boolean
   */
  protected $hasRun;

  /**
   * When set to true any run method will be halted immediately after the SQL
   * has been generated, with the SQL being dumped to the screen.
   *
   * @var boolean
   */
  protected $dumpSQL;

  /**
   * Initialize the Set object
   *
   * @param \Metrol\Db\Item
   */
  public function __construct(\Metrol\Db\Item $obj)
  {
    parent::__construct();

    $this->source     = $obj->getSource();
    $this->setItemType($obj);
    $this->sqlEngine  = $obj->getSource()->getSQLEngine();
    $this->hasRun     = false;
    $this->dumpSQL    = false;
  }

  /**
   * Diagnostic output of the contents of this list
   *
   * @return string
   */
  public function __toString()
  {
    $tableTitle = 'Diagnostic output from: '.get_class($this);
    $tableTitle .= '<br />Result Count: '.$this->count();

    $t = new \Metrol\HTML\Table($tableTitle);
    $t->setSpacing(0)->setPadding(5);
    $t->setStyle('font-family', 'monospace')
      ->setStyle('font-size', '8pt')
      ->setStyle('border', '2px solid #800000');

    $t->setCaption($tableTitle);
    $t->getCaption()
      ->setStyle('text-align', 'left')
      ->setStyle('background-color', '#dfdfdf')
      ->setStyle('padding', '6px')
      ->setStyle('font-weight', 'bold');

    $t->striping()->setRowColor('#dddddd');

    $qryMsg = 'Last Query Performed<br /><hr />';
    $qryMsg .= '<pre>'.$this->sqlEngine->getLastQuery().'</pre>';
    $qryMsg .= '<hr />';

    $t->setBefore($qryMsg);

    if ( $this->count() == 0 )
    {
      $t->addRow('No data in the set');

      return $t->output();
    }

    $this->rewind();
    $fields = $this->current()->getFields();

    $t->setHeadActive();
    $t->addRow();

    $t->addCell('PriKey')
      ->addStyle('border-bottom', '2px solid black')
      ->addStyle('border-right', '2px solid black');

    foreach ( $fields as $field )
    {
      $t->addCell($field)
        ->addStyle('border-bottom', '2px solid black')
        ->addStyle('border-right', '2px solid black');
    }

    $t->setBodyActive();

    foreach ( $this->dataSet as $idx => $item )
    {
      $t->addRow();

      $t->addCell( $idx )
        ->addStyle('border-right', '1px solid black')
        ->addStyle('font-weight', 'bold');

      foreach ( $fields as $field )
      {
        $cellContent = '&nbsp;';

        if ( $item->$field === null )
        {
          $cellContent = '<i>null</i>';
        }
        else if ( is_object($item->$field) )
        {
          $cellContent = $item->$field;
          $cellContent .= ' ('.get_class($item->$field).')';
        }
        else if ( is_bool($item->$field) )
        {
          if ( $item->$field )
          {
            $cellContent = '[true]';
          }
          else
          {
            $cellContent = '[false]';
          }
        }
        else
        {
          $cellContent = $item->$field;
        }

        $t->addCell( $cellContent )
            ->addStyle('border-right', '1px solid black');
      }
    }

    return strval($t->output());
  }

  /**
   * Execute the query and store the results into the dataSet member.
   * If a key field is provided, it will be used to index the set.  Otherwise,
   * the index will simply increment.
   *
   * @param string Field name to index the set on
   * @return this
   */
  public function run($key = null)
  {
    $sql = $this->sqlEngine->output();

    if ( $this->dumpSQL )
    {
      print "\n\n<pre>\n$sql\n</pre>\n";
      exit;
    }

    $qr = $this->source->driver->queryNoCache($sql);

    if ( $this->source->driver->numRows($qr) == 0 )
    {
      return $this;
    }

    while ( $r = $this->source->driver->fetchAssoc($qr) )
    {
      $record = $this->emptyItem();

      foreach ( $r as $field => $value )
      {
        $record->$field = $value;
      }

      if ( array_key_exists($key, $r) )
      {
        $this->add($record, $r[$key]);  // Assign the index
      }
      else
      {
        $this->add($record); // Increment only
      }
    }

    $this->hasRun = true;

    return $this;
  }

  /**
   * Execute the query and return an array of unique values from the specified
   * field.
   * No objects are created or stored here.
   *
   * @param string Field name to return information on
   * @return array
   */
  public function runForField($field = null)
  {
    $sql = $this->sqlEngine->output();

    if ( $this->dumpSQL )
    {
      print "\n\n<pre>\n$sql\n</pre>\n";
      exit;
    }

    $drv = $this->source->driver;
    $rtn = array();

    $qr = $drv->queryNoCache($sql);

    if ( $drv->numRows($qr) == 0 )
    {
      return $rtn;
    }

    while ( $r = $drv->fetchAssoc($qr) )
    {
      if ( array_key_exists($field, $r) )
      {
        $rtn[] = $r[$field];
      }
    }

    $this->hasRun = true;

    return $rtn;
  }

  /**
   * Runs the assembled query, but only returns a count and does not store any
   * information into this object.
   *
   * @return integer
   */
  public function runForCount()
  {
    $sqlEng = $this->sqlEngine;

    // Running for a count will not respect limit or offset settings.  Back up
    // whatever is there, then blank them both.
    $origLimit  = $sqlEng->getLimit();
    $origOffset = $sqlEng->getOffset();
    $sqlEng->setLimit(0)->setOffset(0);

    $sql = $sqlEng->output();

    if ( $this->dumpSQL )
    {
      print "\n\n<pre>\n$sql\n</pre>\n";
      exit;
    }

    // Get the row count from the query
    $drv  = $this->source->driver;
    $qr   = $drv->queryNoCache($sql);
    $rows = $drv->numRows($qr);

    // Put the limit and offset back to where they were
    $sqlEng->setLimit($origLimit)->setOffset($origOffset);

    $this->hasRun = true;

    return $rows;
  }

  /**
   * Provides an array ready to push into a SELECT form object of the data in
   * object.  You must specify which field will appear to the user, while the
   * primary key will be used as the value.
   *
   * @param string Which field to display
   * @return array
   */
  public function getSelectArray($fieldToDisplay)
  {
    $rtn = array();

    foreach ( $this as $idx => $obj )
    {
      $rtn[$idx] = $obj->getValue($fieldToDisplay);
    }

    return $rtn;
  }

  /**
   * Provide the Source object assigned to the record type of this data set
   *
   * @return \Metrol\Db\Source
   */
  public function getSource()
  {
    return $this->source;
  }

  /**
   * Sets the dumpSQL flag for diagnostic purposes
   *
   * @param boolean
   * @return this
   */
  public function setDumpSQL($flag)
  {
    if ( $flag )
    {
      $this->dumpSQL = true;
    }
    else
    {
      $this->dumpSQL = false;
    }

    return $this;
  }

  /**
   * Provide the SQL engine to external callers that may need to manually
   * tweak at a lower level
   *
   * @return \Metrol\Db\SQL
   */
  public function getSQLEngine()
  {
    return $this->sqlEngine;
  }

  /**
   * Adds a boolean filter to the result set.
   * For database access, this goes into the WHERE clause
   *
   * @param string
   * @return this
   */
  public function addFilter($filterClause)
  {
    $this->sqlEngine->where($filterClause);

    return $this;
  }

  /**
   * Remove a filter from the list based on the clause passed in.
   *
   * @param string
   * @return this
   */
  public function removeFilter($filterClause)
  {
    $this->sqlEngine->removeWhereClause($filterClause);

    return $this;
  }

  /**
   * Resets all filters back to an initial state.
   *
   * @return this
   */
  public function resetFilters()
  {
    $this->sqlEngine->resetFilters();
    $this->hasRun = false;

    return $this;
  }

  /**
   * Add a filter on the result set based on a Data Record
   *
   * @param \Metrol\Db\Item\Record Database record to filter on
   * @param string Field that the Record Key would match with
   * @param alias When dealing with multiple data sources
   * @return this
   */
  public function addRecordFilter(\Metrol\Db\Item\Record $item,
                                  $keyField = null, $alias = 'obj')
  {
    $this->sqlEngine->whereRecord($item, $keyField, $alias);

    return $this;
  }

  /**
   * Filter the results where a field has a value in one of the items in the
   * provide array
   *
   * @param string Field a value must be in
   * @param array The list of values
   * @return this
   */
  public function addInArrayFilter($field, array $inList, $alias = 'obj')
  {
    if ( count($inList) == 0 )
    {
      return $this;
    }

    $quoteFlag = true;

    // Sample the first item in the list and "assume" the rest of those items
    // will need quoted the same way.
    reset($inList);
    $sample = current($inList);

    if ( is_numeric($sample) )
    {
      $quoteFlag = false;
    }

    if ( $quoteFlag )
    {
      $valList = "('".implode("', '", $inList)."')";
    }
    else
    {
      $valList = '('.implode(', ', $inList).')';
    }

    $sql  = '"'.$alias.'"."'.$field.'" IN '.$valList;

    $this->addFilter($sql);

    return $this;
  }

  /**
   * Fields entered here will restrict the list to unique values of those
   * fields.
   *
   * @param  string
   * @return  this
   */
  public function addDistinctField($fieldName)
  {
    $this->sqlEngine->addDistinctField($fieldName);

    return $this;
  }

  /**
   * Specifies which fields to order this list by
   *
   * @param string Which field to sort on
   * @param string Direction of the sort.  ASC or DESC.
   * @param alias When dealing with multiple data sources
   * @return this
   */
  public function setOrder($field, $direction = 'ASC', $alias = 'obj')
  {
    $this->sqlEngine->setOrder($field, $direction, $alias);

    return $this;
  }

  /**
   * Sets a limit on how many records to get
   *
   * @param integer
   * @return this
   */
  public function setLimit($resultLimit)
  {
    $this->sqlEngine->setLimit($resultLimit);

    return $this;
  }

  /**
   * Sets the offset for where to begin getting data records
   *
   * @param integer
   * @return this
   */
  public function setOffset($resultOffset)
  {
    $this->sqlEngine->setOffset($resultOffset);

    return $this;
  }

  /**
   * Provide the last query run from this object
   *
   * @return string
   */
  public function getLastQuery()
  {
    return $this->sqlEngine->getLastQuery();
  }
}
