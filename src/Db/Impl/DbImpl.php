<?php

namespace WebX\Db\Impl;
use WebX\Db\Db;
use WebX\Db\DbListener;
use WebX\Db\QueryEscaper;
use WebX\Db\DbException;
use WebX\Db\DbDeadlockException;
use WebX\Db\DbKeyException;
use WebX\Db\RowWrapperFactory;

class DbImpl implements Db {
	/**
	 * @var \mysqli
	 */
	private $mysql;

	/**
	 * @var int
	 */
	private $txCount = 0;

	/**
	 * @var MysqlInstanceProvider
	 */
	private $instanceProvider;

	/**
	 * @var DbListener[]|null
	 */
	private $listeners;

	/**
	 * @var
	 */
	private $rowWrapperFactory;

	/**
	 * Creates a new DB.
	 * @param array|\mysqli|MysqlInstanceProvider $config
	 * @throws DbException
	 */
	public function __construct($mysqlConfig, array $config = null) {
		if(is_array($mysqlConfig)) {
			$this->instanceProvider = new MysqlInstanceProviderArray($mysqlConfig);
		} else if ($mysqlConfig instanceof \mysqli) {
			$this->instanceProvider = new MysqlInstanceProviderConnection($mysqlConfig);
		} else if ($mysqlConfig instanceof MysqlInstanceProvider) {
			$this->instanceProvider = $mysqlConfig;
		} else {
			throw new DbException("Db must be configured with an array, mysqli instance or MysqlInstanceProvider");
		}

		if($rowWrapperFactory = Properties::any("rowWrapperFactory",$config,false)) {
			if($rowWrapperFactory instanceof RowWrapperFactory) {
				$this->rowWrapperFactory = $rowWrapperFactory;
			} else if (is_string($rowWrapperFactory)) {
				$this->rowWrapperFactory = new $rowWrapperFactory();
			} else {
				throw new DbException("config.rowWrapperFactory must be either an instance of RowWrapperFactory or a class name");
			}
		} else {
			$this->rowWrapperFactory = new DefaultRowWrapperFactory();
		}
 	}
	
	private function savePointId() {
		return "sp" . $this->txCount;
	}
	
	public function startTx() {
		if($this->txCount===0) {
			$this->execute("START TRANSACTION");
		} else {
			$this->execute("SAVEPOINT {$this->savePointId()}");
		}
		$this->txCount++;
	}
	
	public function commitTx() {
		$this->txCount--;
		if($this->txCount===0) {
			$this->execute("COMMIT");	
		}
	}
	
	public function rollbackTx() {
		$this->txCount--;
		if($this->txCount === 0) {
			$this->execute("ROLLBACK");
		} else {
			$this->execute("ROLLBACK TO SAVEPOINT {$this->savePointId()}");
		}
	}

	public function escape($value) {
		$this->initConnection();
		return $this->escapeInternal($value);
	}

	private function escapeInternal($value) {
		if($value!==NULL) {
			if(is_string($value)) {
				return "'" . $this->mysql->real_escape_string($value) . "'";
			} else if (is_int($value)) {
				return $value;
			} elseif(is_float($value) || is_double($value)) {
				//Localization may add commas.
				return str_replace(",",".",strval($value));
			} else if (is_array($value) || is_object($value)) {
				$escapedArray = array();
				foreach($value as $v) {
					$escapedArray[] = $this->escapeInternal($v);
				}
				return implode(",", $escapedArray);
			} else if(is_bool($value)) {
				return $value ? "1" : "0";
			} else {
				return $value;
			}
		}
		return "NULL";
	}

	/**
	 * Returns the given sql with inserted escaped parameters
	 * @param unknown $sql
	 * @param array $parameters
	 * @return Ambigous <string, unknown>|unknown
	 */
	private function escapeSql($sql,array $parameters=null) {
		if($parameters) {
			$that = $this;
			$replacer = function($matches) use ($parameters,$that) {
				return $that->escapeInternal(@$parameters[$matches[1]]);
			};
			return preg_replace_callback("/:([a-zA-z\d_]+)/i",$replacer,$sql);
		}
		return $sql;
	}
	
	/**
	 * Returns the all rows in the dataset empty array if empty.
	 */
	public function allRows($sql,array $parameters=null) {
		$this->initConnection();
		if($parameters) {
			$sql = $this->escapeSql($sql, $parameters);
		}
		$this->log($sql);
		$rows = array();
		if($result = $this->mysql->query($sql,MYSQLI_STORE_RESULT)) {
			while($row = $result->fetch_assoc()) {
				$rows[] = $this->rowWrapperFactory->create($row);
			}
			$result->close();
		}
		$this->checkDbError($sql);
		return $rows;
	}
	
	/**
	 * Returns the first row in the dataset NULL if empty.
	 */
	public function firstRow($sql,array $parameters=null) {
		$this->initConnection();
		if($parameters) {
			$sql = $this->escapeSql($sql, $parameters);
		}
		$this->log($sql);
		$row = null;
		if($result = $this->mysql->query($sql,MYSQLI_STORE_RESULT)) {
			$row = $this->rowWrapperFactory->create($result->fetch_array());
			$result->close();
		}
		$this->checkDbError($sql);
		return $row;
	}

	public function execute($sql,array $parameters = null) {
		$this->initConnection();
		if($parameters) {
			$sql = $this->escapeSql($sql, $parameters);
		}
		$this->log($sql);
		if(!$this->mysql->real_query($sql)) {
			$this->checkDbError($sql);
		}
	}
	
	public function checkDbError($sql=null) {
		if($errNo = $this->mysql->errno) {
			$msg = $this->mysql->error;
			if($errNo === 1062) {
				if(preg_match("/\sfor\skey\s\'(.*?)\'/i",$msg,$matches)) {
					throw new DbKeyException($matches[1],1062,$msg);
				} else {
					throw new DbException("Key constraint exception by unknown key",1062);
				}
			} else if ($errNo === 1213) {
				throw new DbDeadlockException($msg,1213);
			} else {
				throw new DbException("Db error message:{$msg} SQL:{$sql}",$errNo);
			}
		}
	}

	private function initConnection() {
		if(!$this->mysql) {
			$this->mysql = $this->instanceProvider->instance();
		}
	}
	
	public function insertId() {
		if($this->mysql) {
			return $this->mysql->insert_id;
		}
		return 0;
	}

	public function affectedRows() {
		if($this->mysql) {
			return $this->mysql->affected_rows;
		}
		return -1;
	}

	private function log($sql) {
		if($this->listeners) {
			foreach ($this->listeners as $listener) {
				try {
					$listener->onExecuted($sql);
				} catch (\Exception $e) {
				}
			}
		}
	}

	public function addDbListener(DbListener $listener) {
		if($listener) {
			$this->listeners[] = $listener;
		}
	}
}

?>