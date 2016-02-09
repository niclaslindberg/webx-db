<?php

namespace WebX\Db;

interface RowWrapper {

    public function raw();

    /**
     * Get columns
     * @return string[]
     */
    public function columns();

    /**
     * String value of the column
     * @param string $id
     * @param null $default
     * @return string|null
     */
    public function string($id, $default=null);

    /**
     * Int value of the column
     * @param string $id
     * @param null $default
     * @return int|null
     */
    public function int($id, $default=null);

    /**
     * Float value of the column
     * @param string $id
     * @param null $default
     * @return float|null
     */
    public function float($id, $default=null);

    /**
     * Double value of the column
     * @param string $id
     * @param null $default
     * @return double|null
     */
    public function double($id, $default=null);

    /**
     * Bool value of the column
     * @param string $id
     * @param null $default
     * @return bool|null
     */
    public function bool($id, $default=null);

    /**
     * @param string $id
     * @param null $default
     * @param bool|false $assoc if JSON object is to be returned as associative array or stdObject.
     * @return object|array
     */
    public function json($id, $default=null, $assoc = false);

    /**
     * @param $id
     * @return \DateTime
     */
    public function dateTime($id);

}