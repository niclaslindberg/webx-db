<?php
namespace Nillsoft\Db;
/**
 * A transcation deadlock occured
 * @author niclas
 */
class DbDeadlockException extends DbException {

	public function __construct($message,$dbCode) {
		parent::__construct($message,$dbCode);
	}
}