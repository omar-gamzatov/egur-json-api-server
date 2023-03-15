<?php

declare(strict_types=1);
//egur/Tests/TestingInfoTest.php
namespace Egur\Tests;

require("vendor/autoload.php");

use PHPUnit\Framework\TestCase;
use PDO;
use Egur\Functions\TestingInfo;

class TestingInfoTest extends TestCase 
{
    public function testGetTestingInfo() 
    {
        $testing_info = new TestingInfo();
        try {
            $pdo_link = new PDO('mysql:dbname=test;host=localhost', 'root', '');
        } catch (PDOException) {
            die(json_encode(['error' => 'database connection failed']));
        }
        $data = ['Serial number' => '12.222.2023.03.0001'];
        $result = [
            'param1' => '4235',
            'param2' => '2536',
            'param3' => '3667',
            'param4' => '4562',
            'result' => '1'
        ];
        $this->assertEquals($result, $testing_info->getTestingInfo($data, $pdo_link));

    }
    public function testGetSerialNumberArray() 
    {
        $testing_info = new TestingInfo();
        try {
            $testing_info->pdo_link = new PDO('mysql:dbname=test;host=localhost', 'root', '');
        } catch (PDOException) {
            die(json_encode(['error' => 'database connection failed']));
        }
        $this->assertEquals([
            'depart' => '12',
            'stand' => '123',
            'ordinal' => '2023.03.2203'
        ], $testing_info->getSerialNumberArray('12.123.2023.03.2203'));
    }
    public function testGetSerialNumberId() 
    {
        $testing_info = new TestingInfo();
        try {
            $testing_info->pdo_link = new PDO('mysql:dbname=test;host=localhost', 'root', '');
        } catch (PDOException) {
            die(json_encode(['error' => 'database connection failed']));
        }
        $data = [
            'depart' => '12',
            'stand' => '123',
            'ordinal' => '2023.03.0031'
        ];
        $this->assertEquals('108', $testing_info->getSerialNumberId($data));
    }
    public function testGetTestingInfoBySerialId() 
    {
        $testing_info = new TestingInfo();
        try {
            $testing_info->pdo_link = new PDO('mysql:dbname=test;host=localhost', 'root', '');
        } catch (PDOException) {
            die(json_encode(['error' => 'database connection failed']));
        }

        $data = [
            'depart' => '12',
            'stand' => '222',
            'ordinal' => '2023.03.0001'
        ];

        $this->assertIsNotBool($testing_info->getTestingInfoBySerialId(78, $data));
        $this->assertNotEmpty($testing_info->getTestingInfoBySerialId(78, $data));
        $this->assertNotNull($testing_info->getTestingInfoBySerialId(78, $data));
    }
}
