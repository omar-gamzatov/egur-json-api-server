<?php

declare(strict_types=1);
//egur/Tests/SerialNumberTest.php
namespace Egur\Tests;

require("vendor/autoload.php");

use PDO;

use Egur\Functions\SerialNumber;
use PHPUnit\Framework\TestCase;


final class SerialNumberTest extends TestCase 
{
    public function testGetNewSerialNumber() 
    {
        $data = [
            'depart_num' => '12',
            'stand_num' => '123', 
            'operator' => 'qwert'
        ];

        try {
            $pdo_link = new PDO('mysql:dbname=test;host=localhost', 'root', '');
        } catch (PDOException) {
            die(json_encode(['error' => 'database connection failed']));
        }

        $serial_number = new SerialNumber();

        $this->assertSame('12.123.2023.03.0043', $serial_number->getNewSerialNumber($data, $pdo_link));
    }

    public function testGetLastSerialNumber() 
    {
        $serial_number = new SerialNumber();
        try {
            $serial_number->pdo_link = new PDO('mysql:dbname=test;host=localhost', 'root', '');
        } catch (PDOException) {
            die(json_encode(['error' => 'database connection failed']));
        }
        $this->assertNotEmpty($serial_number->getLastSerialNumber());
        $this->assertNotNull($serial_number->getLastSerialNumber());
        $this->assertIsNotBool($serial_number->getLastSerialNumber());
    }
    public function testCreateNewOrdinalNumber() 
    {
        $serial_number = new SerialNumber();
        try {
            $serial_number->pdo_link = new PDO('mysql:dbname=test;host=localhost', 'root', '');
        } catch (PDOException) {
            die(json_encode(['error' => 'database connection failed']));
        }
        $this->assertEquals('2023.03.0004', $serial_number->createNewOrdinalNumber('2023.03.0003'));
    }
    public function testIsNewMonthOrYear() 
    {
        $serial_number = new SerialNumber();
        $last_date = ['2023', '02'];
        $this->assertEquals(true, $serial_number->isNewMonthOrYear($last_date));
    }
}
