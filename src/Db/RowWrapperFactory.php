<?php

namespace WebX\Db;

interface RowWrapperFactory {


    public function create(array $row);

}