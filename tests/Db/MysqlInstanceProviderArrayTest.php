<?php

namespace WebX\Db;

use WebX\Db\Impl\MysqlInstanceProviderArray;

class MysqlInstanceProviderArrayTest extends TestInit
{

    public function testInstanceCreatedByArray() {
        $settings = self::dbSettings();
        $provider = new MysqlInstanceProviderArray($settings);

        $instance = $provider->instance();
        $this->assertNotNull($instance);
        $this->assertInstanceOf("mysqli",$instance);
    }

}