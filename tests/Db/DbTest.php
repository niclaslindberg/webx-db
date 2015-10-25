<?php

namespace WebX\Db;

use WebX\Db\Impl\DbImpl;
use WebX\Db\Db;
use WebX\Db\DbKeyException;

class DbTest extends TestInit
{

    /**
     * @var Db
     */
    private $db;
    private $tableName;

    public function __construct() {
        $this->tableName = self::$DB_TABLE_NAME;
    }

    public function setUp() {
        parent::setUp();
        $this->db = new DbImpl(self::dbSettings());
    }

    public function testInsertTextAndCheckInsertId() {
        $this->db->execute("INSERT INTO {$this->tableName} (textA) VALUES('a')");
        $insertId = $this->db->insertId();
        $this->assertGreaterThan(0,$insertId);
    }

    public function testInsertMultipleTextAndCheckAffectedRows() {
        $this->db->execute("INSERT INTO {$this->tableName} (textA) VALUES('a'),('b'),('c')");
        $this->assertEquals(3,$this->db->affectedRows());
    }


    public function testInsertValueAndSelectFirstRowWithId() {
        $value = "abc";
        $this->db->execute("INSERT INTO {$this->tableName} (textA) VALUES(:value)",array("value"=>$value));
        $insertId = $this->db->insertId();

        if($row = $this->db->firstRow("SELECT textA FROM {$this->tableName} WHERE id=:id",array("id"=>$insertId))) {
            $this->assertEquals($value,$row["textA"]);
        } else {
            $this->fail("The row was not found.");
        }
    }

    public function testInsertDuplicateValueCausesKeyException() {
        $value = "abc";
        try {
            $this->db->execute("INSERT INTO {$this->tableName} (uniqueCol) VALUES(:value)",array("value"=>$value));
            $this->db->execute("INSERT INTO {$this->tableName} (uniqueCol) VALUES(:value)",array("value"=>$value));
            $this->fail("The second insert statement should have failed. Unqiue constraint");
        } catch(DbKeyException $e) {
            $this->assertEquals(self::$DB_UNIQUE_KEY_NAME,$e->key());
        }
    }

    public function testInsertValueRollbackNoValue() {
        $value = "abc";
        $this->db->startTx();
        $this->db->execute("INSERT INTO {$this->tableName} (textA) VALUES(:value)",array("value"=>$value));
        $row = $this->db->firstRow("SELECT COUNT(*) AS count FROM {$this->tableName}");
        $count = intval($row['count']);
        $this->assertEquals(1,$count);

        $this->db->rollbackTx();
        $row = $this->db->firstRow("SELECT COUNT(*) AS count FROM {$this->tableName}");
        $count = intval($row['count']);
        $this->assertEquals(0,$count);
    }

    public function testHierchicTxMultipleLevelsFailMostInner() {
        $this->db->startTx();
        $this->hierarchicTxInsert("1");

            $this->db->startTx();
            $this->hierarchicTxInsert("2");

                $this->db->startTx();
                $this->hierarchicTxInsert("3");
                $this->db->rollbackTx();

            $this->db->commitTx();
        $this->db->commitTx();
        $this->assertEquals(1,$this->hierarchicTxCount("1"));
        $this->assertEquals(1,$this->hierarchicTxCount("2"));
        $this->assertEquals(0,$this->hierarchicTxCount("3"));
    }

    public function testHierchicTxMultipleLevelsFailAllInner() {
        $this->db->startTx();
        $this->hierarchicTxInsert("1");

            $this->db->startTx();
            $this->hierarchicTxInsert("2");
            $this->db->rollbackTx();

            $this->db->startTx();
            $this->hierarchicTxInsert("3");
            $this->db->rollbackTx();

        $this->db->commitTx();
        $this->assertEquals(1,$this->hierarchicTxCount("1"));
        $this->assertEquals(0,$this->hierarchicTxCount("2"));
        $this->assertEquals(0,$this->hierarchicTxCount("3"));
    }

    private function hierarchicTxInsert($value) {
        $this->db->execute("INSERT INTO {$this->tableName} (textA) VALUES(:value)",array("value"=>$value));
    }

    private function hierarchicTxCount($value) {
        $row = $this->db->firstRow("SELECT COUNT(*) as count FROM {$this->tableName} WHERE textA=:value",array("value"=>$value));
        return intval($row['count']);
    }

    public function testInsertValueCommitValueExists() {
        $value = "abc";
        $this->db->startTx();
        $this->db->execute("INSERT INTO {$this->tableName} (textA) VALUES(:value)",array("value"=>$value));
        $this->db->commitTx();
        $row = $this->db->firstRow("SELECT COUNT(*) AS count FROM {$this->tableName}");
        $count = intval($row['count']);
        $this->assertEquals(1,$count);
    }

    public function testListener() {

    }
}