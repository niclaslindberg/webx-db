<?php

namespace Nillsoft\Db\Impl;
use Nillsoft\Db\DbException;
use Nillsoft\Db\Impl\MysqlInstanceProvider;


class MysqlInstanceProviderConnection implements MysqlInstanceProvider {

    private $instance;

    public function __construct(\mysqli $instance) {
        $this->instance = $instance;
    }

    public function instance() {
        return $this->instance;
    }
}