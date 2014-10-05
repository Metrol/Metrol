<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Driver;

/**
 * Description of Driver
 */
class PostgreSQL implements \Metrol\Db\Driver
{
  /**
   * The Data Source Name that references the connection information needed
   * for this driver
   *
   * @var string
   */
  protected $dsn;

  /**
   * The connection resource to be used for this driver
   *
   * @var connection resource
   */
  protected $connectResource;

  /**
   * The last query resource seen here.
   *
   * @var query resource
   */
  protected $queryResource;

  /**
   * Defines what kind of fetch type will be used when the fetch() method is
   * called
   *
   * @var integer
   */
  protected $fetchType;

  /**
   * Defines the kind of object a fetchObject call will return.
   *
   * @var string
   */
  protected $fetchObjectClass;

  /**
   * Specifies a specific row number from a result set to return.
   *
   * @var integer
   */
  protected $fetchRowNumber;

  /**
   * Initialize the Driver object
   *
   * @param string Data Source Name
   */
  public function __construct()
  {
    $this->connectResource  = null;
    $this->queryResource    = null;
    $this->fetchType        = self::FETCH_OBJECT;
    $this->fetchObjectClass = '';
    $this->fetchRowNumber   = null;
  }

  /**
   * Provide DSN name used for this driver
   *
   * @return string
   */
  public function getDSN()
  {
    return $this->dsn;
  }

  /**
   * Establishes a connection to the specified server and keeps that resource
   * locally for all other method to utilize.
   *
   * @param \Metrol\Db\DSN
   */
  public function connect(\Metrol\Db\DSN $dsn)
  {
    if ( is_resource($this->connectResource) )
    {
      return;
    }

    $connStr = '';

    // The only absolutely required field is the name of the database
    if ( isset($dsn->resourceName) )
    {
      $connStr .= 'dbname='.$dsn->name;
    }
    else
    {
      return;
    }

    if ( isset($dsn->host) )
    {
      $connStr .= ' host='.$dsn->host;
    }

    if ( isset($dsn->port) )
    {
      $connStr .= ' port='.$dsn->port;
    }

    if ( isset($dsn->user) )
    {
      $connStr .= ' user='.$dsn->user;
    }

    if ( isset($dsn->pass) )
    {
      $connStr .= ' password='.$dsn->pass;
    }

    $connStr .= ' connect_timeout=5';

    $this->connectResource = @\pg_connect($connStr) or
            die ('Connection failed: '.$dsn->name.' DSN');

    unset($connStr);
  }

  /**
   * Shuts down the connection to the database, and nulls out the connection
   * resource stored in this object.
   */
  public function close()
  {
    if ( !is_resource($this->connectResource) )
    {
      $this->connectResource = null;
      return; // already closed
    }

    \pg_close($this->connectResource);

    $this->connectResource = null;
  }

  /**
   * Sends a query off to the database and returns the query resource back
   *
   * @param string SQL to send
   * @return resource Query resource
   */
  public function query($sql)
  {
    if ( !is_resource($this->connectResource) )
    {
      throw new \Metrol\Exception('No connection to resource');
    }

    $this->queryResource = \pg_query($this->connectResource, $sql);

    return $this->queryResource;
  }

  /**
   * Same as the query() method, only the query resource is not cached locally
   *
   * @param string SQL to send
   * @return resource Query resource
   */
  public function queryNoCache($sql)
  {
    if ( !is_resource($this->connectResource) )
    {
      throw new \Metrol\Exception('No connection to resource');
    }

    $qr = \pg_query($this->connectResource, $sql);

    return $qr;
  }

  /**
   * Sets the type of fetch object the fetch() method will return
   *
   * @param integer
   * @return this
   */
  public function setFetchType($fetchType)
  {
    switch (intval($fetchType))
    {
      case self::FETCH_OBJECT:
        $this->fetchType = self::FETCH_OBJECT;
        break;

      case self::FETCH_ASSOC:
        $this->fetchType = self::FETCH_ASSOC;
        break;

      case self::FETCH_ROW:
        $this->fetchType = self::FETCH_ROW;
        break;

      default:
        break;
    }

    return $this;
  }

  /**
   * Sets the class name for the kind of object that will be returned from a
   * fetchObject() call.
   *
   * @param string Class name
   * @return this
   */
  public function setFetchObjectClass($class)
  {
    $this->fetchObjectClass = $class;

    return $this;
  }

  /**
   * Sets a specific row number to return from the result set.  This is reset
   * after any fetch operation.
   *
   * @param integer The row to return
   * @return this
   */
  public function setFetchRow($rowNumber)
  {
    $this->fetchRowNumber = intval($rowNumber);

    return $this;
  }

  /**
   * Performs a fetch on a query resource.  The type of record returned is
   * determined by the fetch type specified by setFetchType().
   *
   * @param resource
   * @return mixed
   */
  public function fetch($qr = null, $fetchType = null)
  {
    $rtn = false;

    if ( $fetchType == null )
    {
      $fetchType = $this->fetchType;
    }

    switch ($fetchType)
    {
      case self::FETCH_OBJECT:
        $rtn = $this->fetchObject($qr);
        break;

      case self::FETCH_ROW:
        $rtn = $this->fetchRow($qr);
        break;

      case self::FETCH_ASSOC:
        $rtn = $this->fetchAssoc($qr);
        break;

      default:
        $rtn = false;
        break;
    }

    return $rtn;
  }

  /**
   * Fetch a record from the query resource as an Object
   *
   * @param resource from a query
   * @return stdClass object
   */
  public function fetchObject($qr = null)
  {
    $rtn = false;

    if ( $qr == null )
    {
      $qr = $this->queryResource;
    }

    if ( !is_resource($qr) )
    {
      return $rtn;
    }

    // Check for a specified object to populate
    if ( strlen($this->fetchObjectClass) > 0 )
    {
      $rtn = \pg_fetch_object($qr, $this->fetchRowNumber, $this->fetchObjectClass);
    }
    else
    {
      $rtn = \pg_fetch_object($qr, $this->fetchRowNumber);
    }

    $this->fetchRowNumber = null; // always reset after every fetch

    return $rtn;
  }

  /**
   * Fetch a record from the query resource as an integer indexed array
   *
   * @param resource from a query
   * @return array
   */
  public function fetchRow($qr = null)
  {
    if ( $qr == null )
    {
      $qr = $this->queryResource;
    }

    if ( !is_resource($qr) )
    {
      return false;
    }

    $rtn = \pg_fetch_row($qr, $this->fetchRowNumber);

    $this->fetchRowNumber = null; // always reset after every fetch

    return $rtn;
  }

  /**
   * Fetch a record from the query resource as an associative array
   *
   * @param resource from a query
   * @return array
   */
  public function fetchAssoc($qr = null)
  {
    if ( $qr == null )
    {
      $qr = $this->queryResource;
    }

    if ( !is_resource($qr) )
    {
      return false;
    }

    $rtn = \pg_fetch_assoc($qr, $this->fetchRowNumber);

    $this->fetchRowNumber = null; // always reset after every fetch

    return $rtn;
  }

  /**
   * Provide the number of rows the query result has
   *
   * @return integer
   */
  public function numRows($qr = null)
  {
    if ( $qr == null )
    {
      $qr = $this->queryResource;
    }

    if ( !is_resource($qr) )
    {
      return 0;
    }

    return \pg_num_rows($qr);
  }

  /**
   * Returns the number of tuples (instances/records/rows) affected by
   * INSERT, UPDATE, and DELETE queries.
   *
   * @return integer
   */
  public function affectedRows($qr = null)
  {
    if ( $qr == null )
    {
      $qr = $this->queryResource;
    }

    if ( !is_resource($qr) )
    {
      return 0;
    }

    return \pg_affected_rows($qr);
  }

  /**
   * Provide the number of fields being returned from the result set
   *
   * @return integer
   */
  public function numFields($qr = null)
  {
    if ( $qr == null )
    {
      $qr = $this->queryResource;
    }

    if ( !is_resource($qr) )
    {
      return 0;
    }

    return \pg_num_fields($qr);
  }

  /**
   * Provide a list of all the field names from the result set
   *
   * @return array
   */
  public function resultFieldNames($qr = null)
  {
    $rtn = array();

    if ( $qr == null )
    {
      $qr = $this->queryResource;
    }

    if ( !is_resource($qr) )
    {
      return $rtn;
    }

    $numFields = $this->numFields($qr);

    if ( $numFields == 0 )
    {
      return $rtn;
    }

    for ( $i = 0; $i < $numFields; $i++ )
    {
      $rtn[] = \pg_field_name($qr, $i);
    }

    return $rtn;
  }

  /**
   * Attempts to make the string being provided safe for use in an SQL
   * statement going to this server.
   *
   * @param string
   * @return string Escaped for SQL use
   */
  public function escapeString($text)
  {
    if ( !is_resource($this->connectResource) )
    {
      throw new \Metrol\Exception('No connection to resource');
    }

    $rtn = \pg_escape_string($this->connectResource, $text);

    return $rtn;
  }

  /**
   * Escapes a Database identifier, such as a Table, Schema, or Field name
   *
   * @param string
   * @return string Escaped with quotes around it
   */
  public function escapeIdentifier($ident)
  {
    if ( !is_resource($this->connectResource) )
    {
      throw new \Metrol\Exception('No connection to resource');
    }

    $rtn = \pg_escape_string($this->connectResource, $ident);

    // If everything isn't lower case, then it needs wrapped in double quotes
    if ( strtolower($rtn) != $rtn )
    {
      $rtn = '"'.$rtn.'"';
    }

    return $rtn;
  }

  /**
   * Returns the index field for the table specified.
   *
   * @param \Metrol\Db\Source
   * @return string Name of primary key field
   */
  public function getPrimaryKey(\Metrol\Db\Source $table)
  {
    $sql = <<<SQL
WITH lookup AS
(
  SELECT '{$table->schema}'::text as sch,
         '{$table->name}'::text as tbl
),
schema_ns AS
(
  SELECT oid as relnamespace
    FROM pg_namespace
   WHERE nspname = (SELECT sch FROM lookup)
),
tbl_class AS
(
  SELECT oid AS tblclassid
    FROM pg_class
   WHERE relname = (SELECT tbl FROM lookup)
     AND relnamespace = (SELECT relnamespace FROM schema_ns)
),
indexs AS
(
  SELECT indexrelid
    FROM pg_index
   WHERE indrelid = (SELECT tblclassid FROM tbl_class)
     AND indisprimary = 't'
),
pk AS
(
  SELECT attname AS primary_key
    FROM pg_attribute
   WHERE attrelid = (SELECT indexrelid FROM indexs)
)

SELECT primary_key FROM pk

SQL;

    // Use a local query resource, since nobody else should need this
    $qr = $this->queryNoCache($sql);

    $o = $this->fetchObject($qr);

    return trim($o->primary_key);
  }

  /**
   * Provide the list of allowed values for the specified enumerated type
   *
   * @param string Name of the type
   * @param string Schema where this type is found
   * @return array
   */
  public function getEnumValues($enumType, $schema = 'public')
  {
    $rtn = array();

    $et = $this->escapeString($enumType);
    $sc = $this->escapeString($schema);

    $sql = <<<SQL
SELECT enumlabel
  FROM pg_catalog.pg_enum AS e
  JOIN pg_catalog.pg_type AS t ON e.enumtypid = t.typelem
  JOIN pg_catalog.pg_namespace AS n ON n.oid = t.typnamespace
 WHERE t.typname = '_' || '$et'
   AND n.nspname = '$sc'

SQL;

    $qr = $this->queryNoCache($sql);

    while ( $o = $this->fetchObject($qr) )
    {
      $rtn[] = $o->enumlabel;
    }

    return $rtn;
  }

  /**
   * Inserts a new record into the database.
   * Data to be inserted needs to be in the following array format
   * fldValArray["fieldName"] = "fieldValue"
   * If successful, this method will return the insertID.  A returned ID
   * of 0 either indicates the insert failed, or there wasn't a primary key.
   * Tables without primary keys are still supported.
   *
   * @param \Metrol\Db\Source\Table
   * @param array  Key/Value pairs of database fields
   * @param mixed Primary Key value
   * @return integer The new primary key value, or zero if no key
   * @throws \Metrol\Data\Exception
   */
  public function insert(\Metrol\Db\Source\Table $table, array $values, $pk = null)
  {
    $sql = $this->buildInsertSQL($table, $values, $pk);

    $qr = $this->queryNoCache($sql);

    if ( $pk === null and strlen($table->pk) > 0 )
    {
      $row = $this->fetchRow($qr);
      $rtn = $row[0];
    }
    else if ( $pk !== null )
    {
      $rtn = $pk;
    }
    else
    {
      $rtn = null;
    }

    return $rtn;
  }

  /**
   * Takes the inbound array and turns it into a proper SQL statement for
   * database insertion.
   *
   * @param \Metrol\DB\Source\Table
   * @param array  Key/Value pairs of database fields
   * @return string The Insert SQL statement
   * @todo Put back in a check for field exists.  Removed to deal with primary
   *       key value now getting past.  That would, and should fail an exist
   *       check.
   */
  protected function buildInsertSQL(\Metrol\Db\Source\Table $table, array $values,
                                    $pk = null)
  {
    $sql = '';
    $fldStr = "(";
    $valStr = "(";

    $fSet = $table->fields;

    // If an id value was passed in, the caller is saying that it should go
    // right on into the INSERT as a field.
    if ( $pk !== null and strlen($table->pk) > 0 )
    {
      $values[$table->pk] = $pk;
    }

    foreach ( $values as $field => $value )
    {
      if ( !$fSet->exists($field) and $field !== $table->pk )
      {
        continue;
      }
      else if ( !$fSet->exists($field) and $field == $table->pk )
      {
        $fldType = $table->pkType;
      }
      else
      {
        $fldType = $fSet->$field;
      }

      $fldStr .= '"'.$field.'", ';
      $valStr .= $fldType->getSQLValue($value).', ';
    }

    $fldStr = substr($fldStr, 0, -2).")"; // Strip comma and add a closing ")"
    $valStr = substr($valStr, 0, -2).")"; // Strip comma and add a closing ")"

    $sql = 'INSERT INTO '.$table."\n";
    $sql .= $fldStr."\n";
    $sql .= "VALUES\n";
    $sql .= $valStr;

    // Here's the PostgreSQL trick for getting back an Insert ID.  Only do
    // this if there is a primary key, and it's value has not been set.
    if ( $pk === null and strlen($table->pk) > 0 )
    {
      // Tack on a request for the new primary key value
      $sql .= "\n";
      $sql .= 'RETURNING "'.$table->pk.'"';
    }

    // print "<pre>$sql</pre>\n"; exit;

    return $sql;
  }

  /**
   * Updates information in the specified table from an array.
   *
   * This method is only for tables with a valid primary key, and a known
   * value of that key to use.  Anything more clever then that needs to be
   * put together manually and sent to query().
   *
   * @param \Metrol\Db\Source\Table
   * @param array  Key/Value pairs of database fields
   * @param mixed  Value of the primary key
   */
  public function update(\Metrol\Db\Source\Table $table, array $fldValArray,
                         $pkVal)
  {
    $sql = $this->buildUpdateSQL($table, $fldValArray, $pkVal);

    $this->queryNoCache($sql);
  }

  /**
   * Takes the inbound array and turns it into a proper SQL statement for
   * database insertion.
   * @param \Metrol\Db\Source\Table
   * @param array  Key/Value pairs
   * @param mixed Primary key value
   * @throws \Metrol\Data\Exception
   */
  protected function buildUpdateSQL(\Metrol\Db\Source\Table $table,
                                    array $fldValArray, $pkVal)
  {
    // With an id we need to process for an update
    $sql  = 'UPDATE '.$table."\n";
    $sql .= "   SET\n";

    $fSet = $table->fields;

    // Run through each field to verify it exists, and to check/clean the
    // data stored in there.
    foreach ($fldValArray as $field => $value)
    {
      if ( !$fSet->exists($field) )
      {
        continue;
      }

      $sql .= '  "'.$field.'" = '.$fSet->$field->getSQLValue($value).", \n";
    }

    $sql = substr($sql, 0, -3); // Strip the last comma and line feed off

    $pkValSQL = $table->pkType->getSQLValue($pkVal);

    $sql .= "\n".'WHERE "'.$table->pk.'" = '.$pkValSQL;

    // print "<pre>$sql</pre>\n"; exit;

    return $sql;
  }

  /**
   * Deletes a record from the specified table based on the value of the primary
   * key.
   *
   * @param \Metrol\Db\Source\Table
   * @param mixed Primary Key value
   */
  public function delete(\Metrol\Db\Source\Table $table, $pkVal)
  {
    $sql  = "DELETE\n";
    $sql .= " FROM $table\n";
    $sql .= 'WHERE "'.$table->pk.'" = ';
    $sql .= $table->pkType->getSQLValue($pkVal);

    // print "<pre>$sql</pre>\n"; exit;

    $this->queryNoCache($sql);
  }
}
