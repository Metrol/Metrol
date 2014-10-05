<?php
/**
 * @author Michael Collette <metrol@metrol.net>
 * @package Metrol_Libs
 * @version 2.0
 * @copyright (c) 2014, Michael Collette
 */

namespace Metrol\Db;

/**
 * Describes the API to the SQL generating engine
 */
interface SQL
{
  /**
   * Flags for dealing with how to connect additional tables
   *
   * @const
   */
  const CONNECT_NONE = 0;      // Do not add any WHERE clause
  const CONNECT_ADDED_KEY = 1; // Use the primary key of the added table
  const CONNECT_LOCAL_KEY = 2; // Use the pk of the primary table

  /**
   * Looking to get something out of that engine
   *
   * @return string
   */
  public function output();

  /**
   * Ge the last query that was built
   *
   * @return string
   */
  public function getLastQuery();

  /**
   * Adds a new WHERE clause to the stack
   *
   * @param string
   * @return this
   */
  public function where($whereClause);

  /**
   * Adds a new WHERE clause based on the Data\Record object's primary key
   * that is passed in.
   *
   * @param \Metrol\Data\Record
   * @param string Field in the SQL that the Record Key would match with
   * @param string Table alias for the field.
   * @return this
   */
  public function whereRecord(\Metrol\Db\Item\Record $record,
                              $keyField = null, $alias = 'obj');

  /**
   * Adds a new field to the list of what needs to be sorted on
   *
   * @param string Which field to sort on
   * @param string Direction of the sort.  ASC or DESC.
   * @param string Table alias for the field
   * @return this
   */
  public function setOrder($field, $direction = 'ASC', $alias = 'obj');

  /**
   * Like the orderBy() method, only the field is inserted at the front of the
   * list.
   *
   * @param string Name of the field
   * @param string Either ASC or DESC.
   * @param string Table alias field belongs to
   * @return this
   */
  public function insertOrder($field, $direction = "ASC", $alias = "obj");

  /**
   * Sets the result limit
   *
   * @param integer
   * @return this
   */
  public function setLimit($resultLimit);

  /**
   * Gets what the limit was set for
   *
   * @return integer
   */
  public function getLimit();

  /**
   * Sets the result offset value
   *
   * @param integer
   * @return this
   */
  public function setOffset($resultOffset);

  /**
   * Provide the offset setting
   *
   * @return integer
   */
  public function getOffset();

  /**
   * Fields added with this method will restrict the list to distinct values
   * of that field.  This will be using the DISTINCT ON syntax.
   *
   * @param string $fieldName
   * @return this
   */
  public function addDistinctField($fieldName);

  /**
   * Add an additional table to the query
   *
   * @param \Metrol\Db\Table
   * @param string Alias of the table
   * @param integer How to connect the table.  Use a CONNECT_ constant
   * @return this
   */
  public function addTable(\Metrol\Db\Source $source, $alias, $connectKey = 0);

  /**
   * Provides the ability to INNER JOIN a table with a USING statement.
   *
   * @param \Metrol\Db\Table Table to join
   * @param string Field name (or comma separated list of names) to match on
   * @param string the Alias the join is referred by
   *
   * @return this
   */
  public function joinUsing(\Metrol\Db\Source $source, $field, $alias);

  /**
   * Provides for an INNER JOIN to a table comparing a field with different
   * names.
   *
   * The format this come out in should look like:
   * JOIN table AS alias ON alias.field = joinTo.joinToField
   *
   * @param \Metrol\Db\Table Table to join
   * @param string An alias name for the table to join
   * @param string The field to compare from the table to join
   * @param string The alias reference to the table comparing too
   * @param string The field in the table being compared too.
   *
   * @return this
   */
  public function joinOn(\Metrol\Db\Source $source, $alias, $field,
                         $joinTo, $joinToField);

  /**
   * Provide the list of where clauses that have been added to this set.
   *
   * @return array
   */
  public function getWhereClauses();

  /**
   * Remove a WHERE clause from the stack based on the clause string passed in
   *
   * @param string
   * @return this
   */
  public function removeWhereClause($clause);

  /**
   * Provide the list of Table Joins that have been added
   *
   * @return array
   */
  public function getJoins();

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
  public function joinRaw($alias, $joinSQL);
}
