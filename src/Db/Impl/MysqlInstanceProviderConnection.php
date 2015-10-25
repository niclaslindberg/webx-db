<?php

namespace WebX\Db\Impl;
use WebX\Db\DbException;
use WebX\Db\Impl\MysqlInstanceProvider;


class MysqlInstanceProviderConnection implements MysqlInstanceProvider {

    private $instance;

    public function __construct(\mysqli $instance) {
        $this->instance = $instance;
    }

    public function instance() {
        return $this->instance;
    }
}