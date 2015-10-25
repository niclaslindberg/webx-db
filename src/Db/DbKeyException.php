<?php
namespace Nillsoft\Db;

/**
 * Error for key violations of DB
 * @author niclas
 */
class DbKeyException extends DbException {

	private $key;
	
	public function __construct($key,$dbCode, $msg = null) {
		$this->key = $key;
		parent::__construct($msg ? $msg : "Key constraint violation on {$key}", $dbCode);
	}

	/**
	 * The name (as defined in SQL table definition) of the key that was violated.
	 * @return string
	 */
	public function key() {
		return $this->key;
	}

	public function __toString() {
		return parent::__toString() . " key:[{$this->key}";
	}
}