<?php

namespace Nillsoft\Db;


/**
 * Interface Db
 * @package Nillsoft\Db
 */
interface DbListener
{
    /**
     * Executed for each SQL statement executed.
     * @param string $sql
     * @return void
     */
    public function onExecuted($sql);

}