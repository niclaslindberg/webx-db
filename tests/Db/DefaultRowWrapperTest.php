<?php

namespace WebX\Db;

use WebX\Db\Impl\DefaultRowWrapper;

class DefaultRowWrapperTest extends \PHPUnit_Framework_TestCase
{

    public function testStringValue() {
       $row = array("keyA"=>"valueA");
       $wrapper = new DefaultRowWrapper($row);
       $this->assertEquals("valueA",$wrapper->string("keyA"));

       $default = "ABC";
       $this->assertEquals($default,$wrapper->string("keyB",$default));
    }

    public function testIntValue() {
        $row = array("keyA"=>"1");
        $wrapper = new DefaultRowWrapper($row);
        $this->assertEquals(1,$wrapper->string("keyA"));

        $default = 2;
        $this->assertEquals($default,$wrapper->string("keyB",$default));
    }

    public function testFloatValue() {
        $row = array("keyA"=>"1.1");
        $wrapper = new DefaultRowWrapper($row);
        $this->assertEquals(1.1,$wrapper->string("keyA"));

        $default = 2.0;
        $this->assertEquals($default,$wrapper->string("keyB",$default));
    }

    public function testJsonValue() {
        $array = array("a"=>1);
        $row = array("keyA"=>json_encode($array));
        $wrapper = new DefaultRowWrapper($row);
        $this->assertObjectHasAttribute("a",$wrapper->json("keyA"));
        $this->assertArrayHasKey("a",$wrapper->json("keyA",null,TRUE));

    }


    public function testDateTime() {
        $row = array("dateA"=>"2001-02-03 04:05:06");
        $wrapper = new DefaultRowWrapper($row);
        $dateTime = $wrapper->dateTime("dateA");
        $this->assertNotNull($dateTime);
        $this->assertInstanceOf(\DateTime::class,$dateTime);
        $this->assertEquals("2001-02-03 04:05:06",$dateTime->format("Y-m-d H:i:s"));

    }

}