<?php

namespace WebX\Db;


/**
 * Interface Db
 * @package WebX\Db
 */
interface Db
{

    /**
     * Starts a transaction. If this is subsequent call to startTx() a rollback point is created.
     * @return void
     */
    public function startTx();

    /**
     * Commits the transaction. If this is the outer most transaction the whole transaction is commited.
     * @return void
     */
    public function commitTx();

    /**
     * Rolls back the transaction. If this is the outer most transaction the transaction is rolled back. If not it's rolled back to the previous startTx.
     * @return null
     */
    public function rollbackTx();

    /**
     * Returns
     * @param string $sql
     * @param array|null $parameters
     * @return RowWrapper[]
     */
    public function allRows($sql, array $parameters = null);

    /**
     * @param string $sql
     * @param array|null $parameters
     * @return RowWrapper
     */
    public function firstRow($sql, array $parameters = null);

    /**
     * @param string $sql
     * @param array|null $parameters
     * @return void
     */
    public function execute($sql, array $parameters = null);

    /**
     * Executes the given closure in startTx(), commitTx|rollbackTx calls. If the execution of the closure throws any Exception the transaction will be rolled back otherwise the transaction will be committed.
     * The closure must take the act
     * Ex:
     * executeInTx(function(Db $db) {
     *   $db->execute(sql1);
     *   $db->execute(sql2);
     * });
     *
     * @param \Closure $closure with one parameter Db $db
     * @return mixed The return value of the provided closure
     */
    public function executeInTx(\Closure $closure);

    /**
     * Escapes the value to valid SQL.
     * @param $value
     * @return string
     */
    public function escape($value);
    /**
     *
     * @return int|null
     */
    public function insertId();

    /**
     * @return int|null
     */
    public function affectedRows();


    /**
     * @param DbListener|Closure $listener (if Closure must have the declaration function($sql){})
     * @return void
     */
    public function addDbListener($listener);


    
}