<?php
/**
 * Created by PhpStorm.
 * User: niclas
 * Date: 2015-10-26
 * Time: 07:55
 */

namespace WebX\Db\Impl;


use WebX\Db\RowWrapper;

class DefaultRowWrapper implements RowWrapper
{

    private $row;

    public function __construct(array $row) {
        $this->row = $row;
    }

    public function columns()
    {
        return array_keys($this->row);
    }

    public function raw()
    {
        return $this->row;
    }

    private function read($id) {
        return isset($this->row[$id]) ? $this->row[$id] : null;
    }

    public function string($id, $default = null)
    {
        if(NULL !== ($val = $this->read($id))) {
            return strval($val);
        }
        return $default;
    }

    public function int($id, $default = null)
    {
        if(NULL !== ($val = $this->read($id))) {
            return intval($val);
        }
        return $default;
    }

    public function float($id, $default = null)
    {
        if(NULL !== ($val = $this->read($id))) {
            return floatval($val);
        }
        return $default;
    }

    public function double($id, $default = null)
    {
        if(NULL !== ($val = $this->read($id))) {
            return doubleval($val);
        }
        return $default;
    }

    public function bool($id, $default = null)
    {
        if(NULL !== ($val = $this->read($id))) {
            return boolval($val);
        }
        return $default;
    }

    public function json($id, $default = null, $assoc = false)
    {
        if(NULL !== ($val = $this->read($id))) {
            return json_decode($val,$assoc);
        }
        return $default;
    }
}