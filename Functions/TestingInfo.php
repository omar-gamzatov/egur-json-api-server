<?php

declare(strict_types=1);

namespace Egur\Functions;

use PDO;
use Egur\Functions\Messages;

class TestingInfo
{
    private array $recieved_data;
    public PDO $pdo_link;

    /**
     * Добавляет новую запись в таблице testing_info с полученными от клиента данными.
     * Возвращает кленту json encoded string с результатом выполнения.
     *
     * @param array $recieved_data полученные от клиента данные;
     * @param \PDO $pdo_link экзепляр PDO, представляющий соединение с базой данных.
     */
    public function sendTestingInfo(array $recieved_data, PDO $pdo_link): void
    {
        $this->pdo_link = $pdo_link;
        $this->pdo_link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->recieved_data = $recieved_data;

        $serial_number = $this->recieved_data['Serial number'];
        $serial_array = $this->getSerialNumberArray($serial_number);
        $serial_number_id = $this->getSerialNumberId($serial_array);

        unset($this->recieved_data['Serial number']);
        $this->pdo_link->beginTransaction();
        $pdo_statement = $this->pdo_link->prepare(
            "INSERT INTO `testing_info` (`serial_number_id`, `param1`, `param2`, `param3`, `param4`, `result`)
             VALUES ('$serial_number_id', :param1, :param2, :param3, :param4, :result)"
        );
        try {
            if ($pdo_statement->execute($this->recieved_data)) {
                $this->pdo_link->commit();
                Messages::dieWithMessage('testing info sended');
            }
        } catch (PDOException) {
            $this->pdo_link->rollBack();
            Messages::dieWithError('testing info not sended');
        }
    }

    /**
     * Возвращает кленту запись из БД с тестовой информацией по полученному серийному номеру.
     *
     * @param array $recieved_data полученные от клиента данные;
     * @param PDO $pdo_link экзепляр PDO, представляющий соединение с базой данных;
     * @return array массив с тестовой информацией изделия с указаным серийным номером.
     */
    public function getTestingInfo(array $recieved_data, PDO $pdo_link): array
    {
        $this->pdo_link = $pdo_link;
        $this->pdo_link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->recieved_data = $recieved_data;

        $serial_number = $this->recieved_data['Serial number'];
        $serial_array = $this->getSerialNumberArray($serial_number);
        $serial_number_id = $this->getSerialNumberId($serial_array);

        $result = $this->getTestingInfoBySerialId($serial_number_id, $serial_array);
        Messages::echoArray($result);
        return $result;
    }

    /**
     * Разбивает строку с серийным номер на элементы массива.
     *
     * @param string $serial_number серийный номер изделия;
     * @return array серийный номер в виде массива ['depart' => xx, 'stand' => xxx, 'ordinal' => xxxx.xx.xxxx].
     */
    public function getSerialNumberArray(string $serial_number): array
    {
        $array = explode('.', $serial_number);
        return [
            'depart' => "$array[0]",
            'stand' => "$array[1]",
            'ordinal' => "$array[2].$array[3].$array[4]"];
    }

    /**
     * Получает из БД id записи с полученным от клиента серийным номером.
     *
     * @param array $serial_array серийный номер в виде массива;
     * @return int идентификатор записи с тест. инф.
     */
    public function getSerialNumberId(array $serial_array): int
    {
        $this->pdo_link->beginTransaction();
        $pdo_statement = $this->pdo_link->prepare(
            "SELECT `serial_number_id` 
            FROM `serial_number` WHERE 
            `depart_num` = :depart
            AND `stand_num` = :stand
            AND `serial_num` = :ordinal"
        );
        try {
            $pdo_statement->execute($serial_array);
            $this->pdo_link->commit();
            $pdo_statement->setFetchMode(PDO::FETCH_ASSOC);
            $result = $pdo_statement->fetch();
        } catch (PDOException) {
            $this->pdo_link->rollBack();
            Messages::dieWithError('cannot recieve serial number id');
        }
        if (!$result) {
            Messages::dieWithError('cannot recieve serial number id');
        }
        return $result['serial_number_id'];
    }

    /**
     * Получает из БД массив с атрибутами тестовой информации для заданного серийного номера.
     *
     * @param int $serial_number_id id записи с полученным от клинета серийным номером;
     * @param array $serial_number массив с элементами серийного номера;
     * @return string|array результат выполнения запроса к БД.
     */
    public function getTestingInfoBySerialId(int $serial_number_id, array $serial_number): string|array
    {
        $data = [
            'depart' => $serial_number['depart'],
            'stand' => $serial_number['stand'],
            'ordinal' => $serial_number['ordinal'],
            'serial_number_id' => $serial_number_id
        ];
        $this->pdo_link->beginTransaction();
        $pdo_statement = $this->pdo_link->prepare(
            "SELECT `param1`, `param2`, `param3`, `param4`, `result`
            FROM `testing_info` INNER JOIN `serial_number` ON
            serial_number.depart_num = :depart
            AND serial_number.stand_num = :stand
            AND serial_number.serial_num = :ordinal
            AND testing_info.serial_number_id = :serial_number_id"
        );
        try {
            $pdo_statement->execute($data);
            $this->pdo_link->commit();
            $pdo_statement->setFetchMode(PDO::FETCH_ASSOC);
            return $pdo_statement->fetch();
        } catch (PDOException) {
            $this->pdo_link->rollBack();
            return Messages::getError('cannot recieve testing info');
        }
    }
}
