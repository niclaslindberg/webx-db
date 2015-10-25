<?php

namespace Nillsoft\Db;


/**
 * Interface Db
 * @package Nillsoft\Db
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
     * @return array[]
     */
    public function allRows($sql, array $parameters = null);

    /**
     * @param string $sql
     * @param array|null $parameters
     * @return array[]
     */
    public function firstRow($sql, array $parameters = null);

    /**
     * @param string $sql
     * @param array|null $parameters
     * @return void
     */
    public function execute($sql, array $parameters = null);

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
     * @param DbListener $listener
     * @return void
     */
    public function addDbListener(DbListener $listener);
    
}