<?php
class SerialNumber {
    private array $_recieved_data;
    public PDO $_pdo_link;

    /**
     * Получает из БД послуднюю запись серийного номера, формирует новый серийный номер
     * Если новый серийный номер сформирован, добавляет новую запись в БД
     * @param array $recieved_data полученные от клиента данные
     * @param PDO $pdo_link экзепляр PDO, представляющий соединение с базой данных
     * @return string новый серийный номер вида xx.xxx.xxxx.xx.xxxx
     */
    public function getNewSerialNumber(array $recieved_data, PDO $pdo_link): string {
        $this->_pdo_link = $pdo_link;
        $this->_pdo_link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->_recieved_data = $recieved_data;  

        $last_ordinal_number = $this->getLastSerialNumber();
        $depart = $this->_recieved_data['depart_num'];
        $stand = $this->_recieved_data['stand_num'];
        $ordinal = $this->createNewOrdinalNumber($last_ordinal_number['serial_num']);

        $this->sendNewSerialNumberToDatabase($ordinal);
        echo(json_encode(['Serial number' => "$depart.$stand.$ordinal"]));
        return "$depart.$stand.$ordinal";
    }

    /**
     * Возвращает массив с серийным номером вида ['serial_number_id' => x, 'operator' => x, 'depart_num' => xx,
     * 'stand_num' => xxx, 'serial_num' => xxxx.xx.xxxx, 'date' => xxxx-xx-xx xx:xx:xx] в случае успешного выполнения запроса
     * иначе позвращает на клиент json encoded string вида {'error' : 'одна ошибка и ты ошибся'}
     * @return string|array 
     */
    public function getLastSerialNumber(): string|array {
        $this->_pdo_link->beginTransaction();
        $pdo_statement = $this->_pdo_link->prepare('SELECT * FROM `serial_number` ORDER BY `serial_num` DESC LIMIT 1');
        $pdo_statement->setFetchMode(PDO::FETCH_ASSOC);
        try {
            $pdo_statement->execute();
            $this->_pdo_link->commit();
        } catch (PDOException) {
            $this->_pdo_link->rollBack();
            die(json_encode(['error' => 'last serial number number was not received']));
        }
        return $pdo_statement->fetch();
    }

    /**
     * На основе порядкового номера изделия и текущей даты формирует новый порядковый номер для следующего иделия
     * @param string $last_ordinal_number последний порядковый номер полученый из БД
     * @return string новый орядковый номер вида xxxx.xx.xxxx
     */
    public function createNewOrdinalNumber(string $last_ordinal_number): string {
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

    /**
     * Осуществляет проверку даты последней записи серийного номера из БД, если в текущем месяце записей не было порядковый номер
     * обнуляется (xxxx.xx.xxxx => xxxx.xx.0000) 
     * @param array $last_date год и месяц последней записи
     * @return bool true если в текущем месяце записей не было, false если были
     */
    public function isNewMonthOrYear(array $last_date): bool {
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
    
    /**
     * Записывает новый серийный номер вида xx.xxx.xxxx.xx.xxxx в БД
     * В случае ошибки отправит на клиент json encoded error
     * @param string $ordinal_number новый порядковый номер изделия
     */
    private function sendNewSerialNumberToDatabase(string $ordinal_number): void {
        $data = [
            'operator' => $this->_recieved_data['operator'],
            'depart_num' => $this->_recieved_data['depart_num'],
            'stand_num' => $this->_recieved_data['stand_num'],
            'ordinal' => $ordinal_number
        ];
        $this->_pdo_link->beginTransaction();
        $pdo_statement = $this->_pdo_link->prepare("INSERT INTO `serial_number` (`serial_number_id`, `operator`, `depart_num`, `stand_num`, `serial_num`, `date`)
        VALUES (NULL, :operator, :depart_num, :stand_num, :ordinal, CURRENT_TIMESTAMP)");

        try {
            $pdo_statement->execute($data);
            $this->_pdo_link->commit();
        } catch (PDOException) {
            $this->_pdo_link->rollBack();
            die(json_encode(['error' => 'new serial number not recorded to database']));
        }
    }
}
?>
