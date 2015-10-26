<?php

namespace WebX\Db\Impl;

use WebX\Db\RowWrapperFactory;

class DefaultRowWrapperFactory implements RowWrapperFactory {

    public function create(array $row)
    {
        return new DefaultRowWrapper($row);
    }

}