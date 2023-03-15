<?php

declare(strict_types=1);

namespace Egur;

use PDO;

use Egur\Functions\{SerialNumber, TestingInfo};


class RequestHandler 
{
    private SerialNumber|TestingInfo $request_handler;
    private const GET_SERIAL_NUMBER = 'get_serial_number';
    private const SEND_TESTING_INFO = 'send_testing_info';
    private const GET_TESTING_INFO = 'get_testing_info';
    private const UNKNOWN_REQUEST = 'unknown request parameter';

    /**
     * В зависимости от запроса полученного от клиента выполняет инструкции на получение нового серийного номера для текущего клиента, запись в БД тестовой информации по заданному серийному номеру, получение из БД тестовой информации по заданному серийному номеру.
     * В случае получения некорректного запроса отправляет клиенту json encoded string
     * {'error' : '****'}
     * @param string $request полученный от клиента запрос;
     * @param array $recieved_data полученные от клиента данные;
     * @param PDO $pdo_link экзепляр PDO, представляющий соединение с базой данных.
     */
    public function __construct(string $request, array $recieved_data, PDO $pdo_link) 
    {
        if ($request === self::GET_SERIAL_NUMBER) {
            $this->request_handler = new SerialNumber();
            $this->request_handler->getNewSerialNumber($recieved_data, $pdo_link);
            
        } else if ($request == self::SEND_TESTING_INFO) {
            $this->request_handler = new TestingInfo();
            $this->request_handler->sendTestingInfo($recieved_data, $pdo_link);

        } else if ($request == self::GET_TESTING_INFO) {
            $this->request_handler = new TestingInfo();
            $this->request_handler->getTestingInfo($recieved_data, $pdo_link);

        } else {
            die(json_encode(['error' => self::UNKNOWN_REQUEST]));
        }
    }
}
