<?php

namespace WebX\Db;

abstract class TestInit extends \PHPUnit_Framework_TestCase  {

    /**
     * @var \mysqli
     */
    private static $mysql;
    protected static $DB_TABLE_NAME = "test";
    protected static $DB_UNIQUE_KEY_NAME = "uniqueCol";

    public static function setUpBeforeClass()
    {
        $dbSettings = self::dbSettings(false);
        //MariaDB supress warnings.
        $err_level = error_reporting(E_ALL ^ E_WARNING);
        $mysql = new \mysqli($dbSettings->host,$dbSettings->user,$dbSettings->password,$dbSettings->database);
        error_reporting($err_level);
        if(!$mysql->real_query("DROP TABLE IF EXISTS " . self::$DB_TABLE_NAME)) {
            throw new \Exception("Could not drop test table:" . self::$DB_TABLE_NAME . " Error:" . $mysql->error);
        }
        if(!$mysql->real_query(self::tableDefinition())) {
            throw new \Exception("Could not create test table:" . self::$DB_TABLE_NAME . " Error:" . $mysql->error);
        }
        self::$mysql = $mysql;
    }

    public static function tearDownAfterClass() {
        self::$mysql->close();
    }

    public function setUp() {
        if(!self::$mysql->real_query("TRUNCATE TABLE " . self::$DB_TABLE_NAME)) {
            throw new \Exception("Could not truncate table");
        }
    }


    protected static function dbSettings($asArray = true) {
        $dbSettingsFile = dirname(__DIR__) . "/db_settings.json";
        if($dbSettingsContent = @file_get_contents($dbSettingsFile)) {
            if($dbSettings = json_decode($dbSettingsContent,$asArray)) {
                return $dbSettings;
            }
        } else {
            return (object)array("user"=>null,"password"=>null,"database"=>"webx_db","host"=>"127.0.0.1");
        }
    }

    private static function tableDefinition() {
        $tableName = self::$DB_TABLE_NAME;
        $uniqueKeyName = self::$DB_UNIQUE_KEY_NAME;
        $sql = "
           CREATE TABLE `{$tableName}` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `uniqueCol` VARCHAR(10),
              `intA` int,
              `textA` VARCHAR(100),
              PRIMARY KEY (`id`),
              UNIQUE KEY `{$uniqueKeyName}` (`uniqueCol`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
        ";
        return $sql;
    }
}