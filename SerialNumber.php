<?php
class SerialNumber {
    private $pdo_link;
    private $recieved_data;


    public function __construct($recieved_data, $pdo_link) {
        $this->pdo_link = $pdo_link;
        $this->pdo_link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->recieved_data = $recieved_data;        
    }

    public function getNewSerialNumber(): string {
        $last_ordinal_number = $this->getLastSerialNumber();
        $depart = $this->recieved_data['depart_num'];
        $stand = $this->recieved_data['stand_num'];
        $ordinal = $this->createNewOrdinalNumber($last_ordinal_number['serial_num']);
        $this->sendNewSerialNumberToDatabase($ordinal);
        echo(json_encode(['Serial number' => "$depart.$stand.$ordinal"]));
        return "$depart.$stand.$ordinal";
    }

    public function getLastSerialNumber(): mixed {
        $pdo_statement = $this->pdo_link->query('SELECT * FROM `serial_number` ORDER BY `serial_num` DESC LIMIT 1');
        $pdo_statement->setFetchMode(PDO::FETCH_ASSOC);
        try {
            $pdo_statement->execute();
        } catch (PDOException) {
            die(json_encode(['error' => 'last serial number number was not received']));
        }
        return $pdo_statement->fetch();
    }

    public function createNewOrdinalNumber($last_ordinal_number): string {
        if ($last_ordinal_number === null)
            $ordinal_number = date("Y.m") . "0001";
        else {
            $exploded = explode('.', $last_ordinal_number);
            $date_of_last_serial_number = [$exploded[0], $exploded[1]];
            if ($this->isNewMonthOrYear($date_of_last_serial_number))
                $ordinal_number = date("Y.m") . ".0001";
            else 
                $ordinal_number = date("Y.m") . "." . sprintf("%'.04d", $exploded[2] + 1);
        }
        return $ordinal_number;
    }

    public function isNewMonthOrYear($last_date): bool {
        // Декущая дата
        $current_year = date("Y");
        $current_month = date("m");
        // Дата последнего серийного номера
        $last_year = $last_date[0];
        $last_month = $last_date[1];
        // Условия для обнуления порядкового номера
        $condition1 = $last_year < $current_year;
        $condition2 = $last_month > $current_month && $last_year < $current_year;
        $condition3 = $last_month < $current_month;
        $condition4 = $last_month > $current_month & $last_year >= $current_year;
    
        return ($condition1 || $condition2 || $condition3 || $condition4);
    }
    
    private function sendNewSerialNumberToDatabase($ordinal_number): void {
        
        $data = [
            'operator' => $this->recieved_data['operator'],
            'depart_num' => $this->recieved_data['depart_num'],
            'stand_num' => $this->recieved_data['stand_num'],
            'ordinal' => $ordinal_number
        ];

        $pdo_statement = $this->pdo_link->prepare("INSERT INTO `serial_number` (`serial_number_id`, `operator`, `depart_num`, `stand_num`, `serial_num`, `date`)
        VALUES (NULL, :operator, :depart_num, :stand_num, :ordinal, CURRENT_TIMESTAMP)");

        try {
            $pdo_statement->execute($data);
        } catch (PDOException) {
            die(json_encode(['error' => 'new serial number not recorded to database']));
        }
    }
}
?>