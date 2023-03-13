<?php
class TestingInfo {
    private $pdo_link;
    private $recieved_data;

    //////////////////////////////////////////// send_testing_info //////////////////////////////////////////////
    public function sendTestingInfo($recieved_data, $pdo_link): void {

        $this->pdo_link = $pdo_link;
        $this->pdo_link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->recieved_data = $recieved_data;

        $serial_number = $this->recieved_data['Serial number'];
        $serial_array = $this->getSerialNumberArray($serial_number);
        $serial_number_id = $this->getSerialNumberId($serial_array);
        
        unset($this->recieved_data['Serial number']);

        $pdo_statement = $this->pdo_link->prepare("INSERT INTO `testing_info` (`serial_number_id`, `param1`, `param2`, `param3`, `param4`, `result`)
                                     VALUES ('$serial_number_id', :param1, :param2, :param3, :param4, :result)");
        try {
            $result = $pdo_statement->execute($this->recieved_data);
            if($result) echo(json_encode(['message' => 'testing info sended']));
        } catch (PDOException) {
            echo(json_encode(['error: ' => 'testing info not sended']));
        }
    }

    //////////////////////////////////////////// get_testing_info //////////////////////////////////////////////
    /**
     * 
     */
    public function getTestingInfo($recieved_data, $pdo_link): void {

        $this->pdo_link = $pdo_link;
        $this->pdo_link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->recieved_data = $recieved_data;

        $serial_number = $this->recieved_data['Serial number'];
        $serial_array = $this->getSerialNumberArray($serial_number);
        $serial_number_id = $this->getSerialNumberId($serial_array);
        if ($serial_number_id != null)
            echo(json_encode($this->getTestingInfoBySerialId($serial_number_id, $serial_array)));
        else 
            echo(json_encode(['error: ' => 'cannot recieve serial number id']));
    }

    /**
     * @param string $serial_number серийный номер изделия
     * @return array серийный номер в виде массива 
     */
    public function getSerialNumberArray($serial_number): array {
        $array = explode('.', $serial_number);
        return [
            'depart' => "$array[0]", 
            'stand' => "$array[1]", 
            'ordinal' => "$array[2].$array[3].$array[4]"];
    }

    /**
     * @param array $serial_array серийный номер в виде массива
     * @return string идентификатор записи с тест. инф.
     */
    public function getSerialNumberId($serial_array): mixed {
        $pdo_statement = $this->pdo_link->prepare("SELECT `serial_number_id` 
                                FROM `serial_number` WHERE 
                                `depart_num` = :depart
                                AND `stand_num` = :stand
                                AND `serial_num` = :ordinal");
        try {
            $pdo_statement->execute($serial_array);
            $pdo_statement->setFetchMode(PDO::FETCH_ASSOC);
            $result = $pdo_statement->fetch();
            if(!$result) return null;
            return $result['serial_number_id'];
        } catch (PDOException) {
            return null;
        }
    }

    /**
     * 
     */
    public function getTestingInfoBySerialId($serial_number_id, $serial_number): mixed {
        $data = [
            'depart' => $serial_number['depart'],
            'stand' => $serial_number['stand'],
            'ordinal' => $serial_number['ordinal'],
            'serial_number_id' => $serial_number_id
        ];
        
        $pdo_statement = $this->pdo_link->prepare("SELECT `param1`, `param2`, `param3`, `param4`, `result`
                                    FROM `testing_info` INNER JOIN `serial_number` ON
                                    serial_number.depart_num = :depart
                                    AND serial_number.stand_num = :stand
                                    AND serial_number.serial_num = :ordinal
                                    AND testing_info.serial_number_id = :serial_number_id");
        try {
            $pdo_statement->execute($data);    
            $pdo_statement->setFetchMode(PDO::FETCH_ASSOC);
            return $pdo_statement->fetch();
        } catch (PDOException) {
            return json_encode(['error: ' => 'cannot recieve testing info']);
        }
    }
}
?>
