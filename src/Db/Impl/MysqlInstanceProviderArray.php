<?php

namespace Nillsoft\Db\Impl;
use Nillsoft\Db\DbException;
use Nillsoft\Db\Impl\MysqlInstanceProvider;


class MysqlInstanceProviderArray implements MysqlInstanceProvider {

    private $settings;

    public function __construct(array $settings) {
        if($settings) {
            $this->settings = $settings;
        } else {
            throw new DbException("Misssing settings");
        }
    }

    public function instance() {
        try {
            $settings = $this->settings;
            $host = Properties::string("host", $settings, false, "127.0.0.1");
            $user = Properties::string("user", $settings, false);
            $password = Properties::string("password", $settings, false);
            $database = Properties::string("database", $settings, true);
            $port = Properties::int("port", $settings, false, 3306);
            $autoCommit = Properties::bool("autoCommit",$settings,false,true);
            $charSet = Properties::string("charSet",$settings,false,"utf8");

            //Turn off error reporting for MariaDB compliance.
            $err_level = error_reporting(E_ALL ^ E_WARNING);
            $mysql = new \mysqli($host, $user, $password, $database, $port);
            error_reporting($err_level);
            if($mysql->connect_error) {
                throw new DbException($mysql->connect_error, $mysql->connect_errno);
            }
            if(!$mysql->set_charset($charSet)) {
                throw new DbException("Could not set charset {$charSet}");
            }
            if($autoCommit!==null) {
                if(!$mysql->autocommit($autoCommit)) {
                    throw new DbException("Could not set autoCommit to {$autoCommit}");
                }
            }
            return $mysql;
        } catch (PropertyException $e) {
            throw new DbException("Missing setting property {$e->getProperty()}");
        }
    }
}