<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db\Source\Table;

/**
 * A PostgreSQL table definition.
 */
class PostgreSQL extends \Metrol\Db\Source\Table
{
  /**
   * A cross reference listing of field types to the classes and methods that
   * handle them.
   *
   * @var array
   */
  private $types;

  /**
   * Initialize the PostgreSQL object
   *
   * @param object
   */
  public function __construct(\Metrol\Db\Driver\PostgreSQL $driver,
                               $tableName, $schema = null)
  {
    parent::__construct($driver, $tableName, $schema);

    if ( $schema === null )
    {
      $this->schema = 'public';
    }
    else
    {
      $this->schema = $schema;
    }

    $this->initFieldTypes();
    $this->initFieldSet();
  }

  /**
   * Provide an instance of the SQL Engine that supports this table's server
   *
   * @return \Metrol\Db\SQL
   */
  public function getSQLEngine()
  {
    $sqlEng = new \Metrol\Db\SQL\PostgreSQL($this);

    return $sqlEng;
  }

  /**
   * Initialize the Field Types that will be used for all the
   */
  private function initFieldTypes()
  {
    $this->types = [
      'smallint'                    => 'integer',
      'bigint'                      => 'integer',
      'integer'                     => 'integer',
      'numeric'                     => 'numeric',
      'decimal'                     => 'numeric',
      'character'                   => 'string',
      'text'                        => 'string',
      'character varying'           => 'string',
      'timestamp without time zone' => 'datetime',
      'timestamp with time zone'    => 'datetime',
      'date'                        => 'datetime',
      'time without time zone'      => 'datetime',
      'time with time zone'         => 'datetime',
      'boolean'                     => 'boolean',
      'enum'                        => 'enum',
      'inet'                        => 'network',
      'ARRAY'                       => 'array'];
  }

  /**
   * Initialize all the Field Type objects that make up this table
   */
  protected function initFieldSet()
  {
    $sql = <<<SQL
with
lookup as
(
  select '{$this->schema}'::text as sch,
         '{$this->name}'::text as tbl
),
type_list as
(
  select typname, typnamespace, typtype
    from pg_type
),
fieldlist_prelim as
(
  select column_name, data_type, character_maximum_length, numeric_precision,
         numeric_scale, udt_schema, udt_name, is_nullable
    from information_schema.columns
   where table_schema = (select sch from lookup)
     and table_name   = (select tbl from lookup)
),
fieldlist as
(
  select fp.column_name,
    case when data_type = 'USER-DEFINED' then
    (
      select case
        when tp.typtype = 'b' then
         'base'::varchar
        when tp.typtype = 'c' then
         'composite'::varchar
        when tp.typtype = 'd' then
         'domain'::varchar
        when tp.typtype = 'e' then
         'enum'::varchar
        when tp.typtype = 'p' then
         'psuedo'::varchar
        end
        from type_list as tp
       where tp.typname = fp.udt_name
         and tp.typnamespace =
         (
           select oid
             from pg_namespace as ns
            where ns.nspname = fp.udt_schema
         )
    )
    else
      data_type
    end as data_type,
    character_maximum_length, numeric_precision, numeric_scale,
    udt_schema, udt_name, is_nullable
   from fieldlist_prelim as fp
)

select * from fieldlist

SQL;

    $qr = $this->driver->queryNoCache($sql);

    while ( $o = $this->driver->fetch($qr) )
    {
      switch ( $this->types[$o->data_type] )
      {
        case 'integer':
          $this->integerField($o);
          break;

        case 'string':
          $this->stringField($o);
          break;

        case 'numeric':
          $this->numericField($o);
          break;

        case 'boolean':
          $this->booleanField($o);
          break;

        case 'enum':
          $this->enumeratedField($o);
          break;

        case 'datetime':
          $this->dateTimeField($o);
          break;

        case 'array':
          $this->arrayField($o);
          break;

        default:
          break;
      }
    }

    $pkField = $this->driver->getPrimaryKey($this);

    $this->primaryKey = $this->fields->$pkField;

    $this->fields->delete($pkField);
  }

  /**
   * Set a serials Primary Key's next value index to the specified value
   *
   * @param integer Next value for the Primary Key to be assigned
   * @return this
   */
  public function resetPrimaryKeySequence($nextVal = 1)
  {
    $seqName = $this->getPrimaryKeySequenceName();
    $nextVal = intval($nextVal);

    $sql = "ALTER SEQUENCE $seqName RESTART WITH $nextVal";

    $this->driver->queryNoCache($sql);
  }

  /**
   * Looks up the sequence name for the primary key
   *
   * @return string Name of the Primary Key's sequence
   */
  protected function getPrimaryKeySequenceName()
  {
    $fqtn = $this->getFQTN();
    $pk   = $this->getPrimaryKeyName();

    $sql = "SELECT pg_get_serial_sequence('$fqtn', '$pk') as seqname";

    $qr = $this->driver->queryNoCache($sql);
    $o = $this->driver->fetch($qr);

    return $o->seqname;
  }

  /**
   * Sets up a field type object
   *
   * @param \stdClass
   */
  protected function integerField(\stdClass $o)
  {
    $byteRef = array('int2' => 2,
                     'int4' => 4,
                     'int8' => 8);

    $type = new \Metrol\Db\Field\Integer;
    $type->setBytes($byteRef[$o->udt_name]);
    $type->setFieldName($o->column_name);

    if ( $o->is_nullable == 'YES' )
    {
      $type->setNullOk(true);
    }
    else
    {
      $type->setNullOk(false);
    }

    $this->fields->add($type);
  }

  /**
   * Sets up a field type object
   *
   * @param \stdClass
   */
  protected function stringField(\stdClass $o)
  {
    $type = new \Metrol\Db\Field\String;
    $type->setDbDriver($this->driver);
    $type->setFieldName($o->column_name);

    if ( $o->data_type == 'character varying' )
    {
      $type->setMaxLength($o->character_maximum_length);
    }

    if ( $o->data_type == 'character' )
    {
      $type->setFixedLength($o->character_maximum_length);
    }

    if ( $o->is_nullable == 'YES' )
    {
      $type->setNullOk(true);
    }
    else
    {
      $type->setNullOk(false);
    }

    $this->fields->add($type);
  }

  /**
   * Sets up a field type object
   *
   * @param \stdClass
   */
  protected function numericField(\stdClass $o)
  {
    $type = new \Metrol\Db\Field\Numeric;
    $type->setPrecision($o->numeric_precision);
    $type->setScale($o->numeric_scale);
    $type->setFieldName($o->column_name);

    if ( $o->is_nullable == 'YES' )
    {
      $type->setNullOk(true);
    }
    else
    {
      $type->setNullOk(false);
    }

    $this->fields->add($type);
  }

  /**
   * Sets up a field type object
   *
   * @param \stdClass
   */
  protected function booleanField(\stdClass $o)
  {
    $type = new \Metrol\Db\Field\Boolean;
    $type->setFieldName($o->column_name);

    if ( $o->is_nullable == 'YES' )
    {
      $type->setNullOk(true);
    }
    else
    {
      $type->setNullOk(false);
    }

    $this->fields->add($type);
  }

  /**
   * Sets up a field type object
   *
   * @param \stdClass
   */
  protected function enumeratedField(\stdClass $o)
  {
    $type = new \Metrol\Db\Field\Enumerated;
    $type->setFieldName($o->column_name);

    if ( $o->is_nullable == 'YES' )
    {
      $type->setNullOk(true);
    }
    else
    {
      $type->setNullOk(false);
    }

    $values = $this->driver->getEnumValues($o->udt_name, $o->udt_schema);

    foreach ( $values as $allowedValue )
    {
      $type->addValue($allowedValue);
    }

    $this->fields->add($type);
  }

  /**
   * Sets up a field type object
   *
   * @param \stdClass
   */
  protected function dateTimeField(\stdClass $o)
  {
    $type = new \Metrol\Db\Field\DateTime;
    $type->setDbDateType($o->data_type);
    $type->setFieldName($o->column_name);

    if ( $o->is_nullable == 'YES' )
    {
      $type->setNullOk(true);
    }
    else
    {
      $type->setNullOk(false);
    }

    $this->fields->add($type);
  }

  /**
   * Sets up a field type object for an array field
   *
   * @param \stdClass
   */
  protected function arrayField(\stdClass $o)
  {
    var_dump($o);

    $type = new \Metrol\Db\Field\ArrayDef($fieldType);
    $type->setFieldName($o->column_name);

    if ( $o->is_nullable == 'YES' )
    {
      $type->setNullOk(true);
    }
    else
    {
      $type->setNullOk(false);
    }

    $this->fields->add($type);
  }
}
