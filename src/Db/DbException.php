<?php
namespace WebX\Db;

/**
 * Top exception for all exceptions thrown by DB
 * @package Nillsoft\Db
 */
class DbException extends \Exception {

	private $dbCode;
	
	public function __construct($msg,$dbCode=null) {
		parent::__construct($msg,0,null);
		$this->dbCode = $dbCode;
	}

	public function dbCode() {
		return $this->dbCode;
	}

	public function __toString() {
		return parent::__toString() . " Db error number:{$this->dbCode}";
	}
}