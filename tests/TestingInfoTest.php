<?php
require_once('TestingInfo.php');

use phpunit\Framework\TestCase;

class TestingInfoTest extends TestCase {
    public function testGetTestingInfo() {
        $testing_info = new TestingInfo();

        try {
            $pdo_link = new PDO('mysql:dbname=test;host=localhost', 'root', '');
        } catch (PDOException) {
            die(json_encode(['error' => 'database connection failed']));
        }

    }
    public function testGetSerialNumberArray() {
        $testing_info = new TestingInfo();
        $this->assertEquals([
            'depart' => '12',
            'stand' => '123',
            'ordinal' => '2023.03.2203'
        ], $testing_info->getSerialNumberArray('12.123.2023.03.2203'));
    }
    public function testGetSerialNumberId() {
        $testing_info = new TestingInfo();
        $data = [
            'depart' => '12',
            'stand' => '123',
            'ordinal' => '2023.03.0031'
        ];
        $this->assertEquals('108', $testing_info->getSerialNumberId($data));
    }
    public function testGetTestingInfoBySerialId() {
        $testing_info = new TestingInfo();
        try {
            $pdo_link = new PDO('mysql:dbname=test;host=localhost', 'root', '');
        } catch (PDOException) {
            die(json_encode(['error' => 'database connection failed']));
        }

        $data = [
            'depart' => '12',
            'stand' => '222',
            'ordinal' => '2023.03.0001'
        ];

        $this->assertIsNotBool($testing_info->getTestingInfoBySerialId('78', $data));
        $this->assertNotEmpty($testing_info->getTestingInfoBySerialId('78', $data));
        $this->assertNotNull($testing_info->getTestingInfoBySerialId('78', $data));
    }
}
?>
