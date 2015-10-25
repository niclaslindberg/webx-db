<?php

namespace Nillsoft\Db\Impl;


/**
 * Interface MysqlInstanceProvider
 * @package Nillsoft\Db
 */
interface MysqlInstanceProvider
{

    /**
     * Creates a \mysql instance to be used.
     * @return \mysqli
     * @throws DbException
     */
    public function instance();

}