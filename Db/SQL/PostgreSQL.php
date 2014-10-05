<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\SQL;

/**
 * An Record Set SQL generator for PostgreSQL
 */
class PostgreSQL implements \Metrol\Db\SQL
{
  /**
   * The base source object where the data will be coming from
   *
   * @var \Metrol\Db\Source
   */
  protected $source;

  /**
   * List of WHERE clauses that will go into the SQL.
   * Each clause is added with AND.
   *
   * @var array
   */
  private $whereClauses;

  /**
   * List of fields and attributes for what to sort the results on
   *
   * @var array
   */
  private $orderList;

  /**
   * Specifies how many records max to pull from the database
   *
   * @var integer
   */
  private $limitVal;

  /**
   * How far into a result set should the database start retrieving rows
   *
   * @var integer
   */
  private $offsetVal;

  /**
   * Specifies additional tables that will be utilized in the SQL
   *
   * @var array
   */
  private $extraTables;

  /**
   * A list of table joins.
   *
   * Array format is:
   * $arr['alias'] = 'Join text';
   *
   * @var array
   */
  private $tableJoins;

  /**
   * List of fields that should be distinct in the return set
   *
   * @var array
   */
  private $distinctFields;

  /**
   * The last query that was built.
   *
   * @var string
   */
  private $lastQuery;

  /**
   * Initialize the SQL engine with the DB record that will be central to the
   * data set to be generated.
   *
   * @param \Metrol\Db\Source
   */
  public function __construct(\Metrol\Db\Source $source)
  {
    $this->source = $source;

    $this->resetFilters();
  }

  /**
   * The other way to get the output from this class
   *
   * @return string
   */
  public function __toString()
  {
    return $this->buildSQL();
  }

  /**
   * Produces the SQL this class was built to create
   *
   * @return string
   */
  public function output()
  {
    $rtn = $this->buildSQL();

    return $rtn;
  }

  /**
   * Sets all the filters back to their initial state
   *
   */
  public function resetFilters()
  {
    $this->whereClauses   = array();
    $this->orderList      = array();
    $this->extraTables    = array();
    $this->tableJoins     = array();
    $this->distinctFields = array();

    $this->limitVal  = 0;
    $this->offsetVal = 0;

    $this->lastQuery = '';
  }

  /**
   * Ge the last query that was built by this class
   *
   * @return string
   */
  public function getLastQuery()
  {
    return $this->lastQuery;
  }

  /**
   * Adds a new WHERE clause to the stack.  Will not allow duplicate entries.
   *
   * @param string
   * @return this
   */
  public function where($whereClause)
  {
    // Prevent duplicates from being added
    if ( in_array($whereClause, $this->whereClauses) )
    {
      return $this;
    }

    $this->whereClauses[] = $whereClause;

    return $this;
  }

  /**
   * Adds a new WHERE clause based on the Data\Record object's primary key
   * that is passed in.
   *
   * @param \Metrol\Db\Item\Record
   * @param string Field in the SQL that the Record Key would match with
   * @param string Table alias for the field.
   * @return this
   */
  public function whereRecord(\Metrol\Db\Item\Record $record,
                              $keyField = null, $alias = 'obj')
  {
    if ( $keyField === null )
    {
      $keyField = $record->primaryKey;
    }

    $cls = '"'.$alias.'"."'.$keyField.'"';
    $cls .= ' = '.$record->id;

    $this->where($cls);

    return $this;
  }

  /**
   * Adds a new field to the list of what needs to be sorted on
   *
   * @param string Which field to sort on
   * @param string Direction of the sort.  ASC or DESC.
   * @param string Table alias for the field
   * @return this
   */
  public function setOrder($field, $direction = 'ASC', $alias = 'obj')
  {
    $dir = strtoupper($direction);

    if ( $dir != 'ASC' and $dir != 'DESC' )
    {
      $dir = 'ASC';
    }

    $idx = count($this->orderList);

    $this->orderList[$idx]['Alias']     = $alias;
    $this->orderList[$idx]['Field']     = $field;
    $this->orderList[$idx]['Direction'] = $dir;

    return $this;
  }

  /**
   * Like the orderBy() method, only the field is inserted at the front of the
   * list.
   *
   * @param string Name of the field
   * @param string Either ASC or DESC.
   * @param string Table alias field belongs to
   * @return this
   */
  public function insertOrder($field, $direction = "ASC", $alias = "obj")
  {
    $this->orderList = array_reverse($this->orderList);
    $this->order($field, $direction, $alias);
    $this->orderList = array_reverse($this->orderList);

    return $this;
  }

  /**
   * Sets the result limit
   *
   * @param integer
   * @return this
   */
  public function setLimit($resultLimit)
  {
    $this->limitVal = intval($resultLimit);

    return $this;
  }

  /**
   * Gets the limit setting
   *
   * @return integer
   */
  public function getLimit()
  {
    return $this->limitVal;
  }

  /**
   * Sets the result offset value
   *
   * @param integer
   * @return this
   */
  public function setOffset($resultOffset)
  {
    $this->offsetVal = intval($resultOffset);

    return $this;
  }

  /**
   * Provides the offset value setting
   *
   * @return integer
   */
  public function getOffset()
  {
    return $this->offsetVal;
  }

  /**
   * Fields added with this method will restrict the list to distinct values
   * of that field.  This will be using the DISTINCT ON syntax.
   *
   * @param string $fieldName
   * @return this
   */
  public function addDistinctField($fieldName)
  {
    $this->distinctFields[] = $fieldName;

    return $this;
  }

  /**
   * Allows additional tables to be included with the Record's table.
   *
   * @param \Metrol\Db\Source
   * @param string SQL Table alias
   * @param integer How to connect the new table to the primary table
   * @return this
   */
  public function addTable(\Metrol\Db\Source $table, $alias, $connectKey = 0)
  {
    $this->extraTables[$alias] = $table;

    switch ($connectKey)
    {
      case self::CONNECT_NONE:
        // Nothing to do.  Up to the caller to manually put in criteria

        break;

      case self::CONNECT_LOCAL_KEY:
        // Going to use the local key
        $pkField = $this->sampleItem->getSource()->primaryKey;

        $cls  = 'obj.';
        $cls .= '"'.$pkField.'"';
        $cls .= ' = ';
        $cls .= '"'.$alias.'".';
        $cls .= '"'.$pkField.'"';

        $this->where($cls);
        break;

      case self::CONNECT_ADDED_KEY:
        // Using the primary key of the added table
        $pkField = $table->primaryKey;

        $cls  = 'obj.';
        $cls .= '"'.$pkField.'"';
        $cls .= ' = ';
        $cls .= '"'.$alias.'".';
        $cls .= '"'.$pkField.'"';

        $this->where($cls);

        break;

      default:
        break;
    }

    return $this;
  }

  /**
   * Provides the ability to INNER JOIN a table with a USING statement.
   *
   * @param \Metrol\Db\Source Table/View to join
   * @param string Field name (or comma separated list of names) to match on
   * @param string the Alias the join is referred by
   *
   * @return this
   */
  public function joinUsing(\Metrol\Db\Source $source, $field, $alias)
  {
    $j = 'JOIN '.$source->fqtn.' AS "'.$alias.'"';

    if ( strpos($field, ',') !== false )
    {
      $flStr = '';
      $fieldList = explode(',', $field);
      $delim = ', ';

      foreach ( $fieldList as $fn )
      {
        $fn = str_replace('"', '', trim($fn));
        $flStr .= '"'.$fn.'"';
        $flStr .= $delim;
      }

      $field = substr($flStr, 0, strlen($delim) * -1);
    }
    else
    {
      $field = '"'.$field.'"';
    }

    $j .= ' USING ('.$field.')';

    $this->tableJoins[$alias] = $j;

    return $this;
  }

  /**
   * Provides for an INNER JOIN to a table comparing a field with different
   * names.
   *
   * The format this come out in should look like:
   * JOIN table AS alias ON alias.field = joinTo.joinToField
   *
   * @param \Metrol\Db\Source The Table/View to join
   * @param string An alias name for the table to join
   * @param string The field to compare from the table to join
   * @param string The alias reference to the table comparing too
   * @param string The field in the table being compared too.
   *
   * @return this
   */
  public function joinOn(\Metrol\Db\Source $source, $alias, $field, $joinTo,
                         $joinToField)
  {
    $j = 'JOIN '.$source->fqtn;
    $j .= ' AS "'.$alias.'"';
    $j .= ' ON "'.$alias.'"."'.$field.'"';
    $j .= ' = ';
    $j .= '"'.$joinTo.'"."'.$joinToField.'"';

    $this->tableJoins[$alias] = $j;

    return $this;
  }

  /**
   * Due to the complex nature of JOINS this method will allow a raw text string
   * to be placed in the SQL where a join line would go.
   * Use with caution.
   *
   * @param string A required alias name for the join
   * @param string The raw SQL for a single JOIN line.
   *
   * @return this
   */
  public function joinRaw($alias, $joinSQL)
  {
    $this->tableJoins[$alias] = $joinSQL;

    return $this;
  }

  /**
   * Provide the list of where clauses that have been added to this set.
   *
   * @return array
   */
  public function getWhereClauses()
  {
    return $this->whereClauses;
  }

  /**
   * Remove a WHERE clause from the stack based on the clause string passed in
   *
   * @param string
   * @return this
   */
  public function removeWhereClause($clause)
  {
    if ( !in_array($clause, $this->whereClauses) )
    {
      return $this; // Nothing to remove
    }

    $keys = array_keys($this->whereClauses, $clause);

    foreach ( $keys as $whereKey )
    {
      unset($this->whereClauses[$whereKey]);
    }

    return $this;
  }

  /**
   * Provide the list of Table Joins that have been added
   *
   * @return array
   */
  public function getJoins()
  {
    return $this->joins;
  }

  /**
   * Assemble the SQL from all the data that's been assembled here.
   *
   * @return string
   */
  private function buildSQL()
  {
    $sql  = 'SELECT ';

    if ( count($this->distinctFields) > 0 )
    {
      $sql .= 'DISTINCT ON ';
      $sql .= '('.implode(',', $this->distinctFields).')';
      $sql .= ' ';
    }

    $sql .= 'obj.*'."\n";
    $sql .= '  FROM '.$this->source->fqtn.' AS obj';

    foreach ( $this->extraTables as $alias => $table )
    {
      $sql .= ",\n".'       '.$table->fqtn;
    }

    if ( count($this->tableJoins) > 0 )
    {
      foreach ( $this->tableJoins as $alias => $joinLine )
      {
        $sql .= "\n";
        $sql .= '  '.$joinLine;
      }
    }

    if ( count($this->whereClauses) > 0 )
    {
      $sql .= "\n".' WHERE ';
      $joinWhere = "\n   AND ";

      foreach ( $this->whereClauses as $clause )
      {
        $sql .= $clause;
        $sql .= $joinWhere;
      }

      $sql = substr($sql, 0, strlen($joinWhere) * -1);
    }

    if ( count($this->orderList) > 0 )
    {
      $sql .= "\n".' ORDER BY ';
      $joinOrder = ', ';

      foreach ( $this->orderList as $orderArray )
      {
        $sql .= '"'.$orderArray['Alias'].'"';
        $sql .= '.';
        $sql .= '"'.$orderArray['Field'].'"';
        $sql .= ' ';
        $sql .= $orderArray['Direction'];
        $sql .= $joinOrder;
      }

      $sql = substr($sql, 0, strlen($joinOrder) * -1);
    }

    if ( $this->limitVal > 0 )
    {
      $sql .= "\n LIMIT ".$this->limitVal;
    }

    if ( $this->offsetVal > 0 )
    {
      $sql .= "\n OFFSET ".$this->offsetVal;
    }

    $this->lastQuery = $sql;

    return $sql;
  }
}
