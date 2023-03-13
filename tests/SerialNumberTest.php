<?
require_once('SerialNumber.php');
use PHPUnit\Framework\TestCase;

final class SerialNumberTest extends TestCase {

    
    public function testGetNewSerialNumber() {
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

        $serial_number = new SerialNumber($data, $pdo_link);

        $this->assertSame('12.123.2023.03.0033', $serial_number->getNewSerialNumber());
    }

    public function testGetLastSerialNumber() {
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

        $serial_number = new SerialNumber($data, $pdo_link);

        $this->assertNotEmpty($serial_number->getLastSerialNumber());
        $this->assertNotNull($serial_number->getLastSerialNumber());
        $this->assertIsNotBool($serial_number->getLastSerialNumber());
    }
    public function testCreateNewOrdinalNumber() {
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
        
        $serial_number = new SerialNumber($data, $pdo_link);

        $this->assertEquals('2023.03.0004', $serial_number->createNewOrdinalNumber('2023.03.0003'));
    }
    public function testIsNewMonthOrYear() {
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
        
        $serial_number = new SerialNumber($data, $pdo_link);

        $this->assertEquals(true, $serial_number->isNewMonthOrYear('2023.02'));
    }
}