<?php
require_once("TestingInfo.php");
require_once("SerialNumber.php");

class RequestHandler {
    private SerialNumber|TestingInfo $request_handler;

    /**
     * В зависимости от запроса полученного от клиента выполняет инструкции на
     * получение нового серийного номера для текущего клиента,
     * запись в БД тестовой информации по заданному серийному номеру,
     * получение из БД тестовой информации по заданному серийному номеру
     * @param string $request полученный от клиента запрос
     * @param array $recieved_data полученные от клиента данные
     * @param PDO $pdo_link экзепляр PDO, представляющий соединение с базой данных
     * 
     * В случае получения некорректного запроса отправляет клиенту json encoded string
     * {'error' : '****'}
     */
    public function __construct(string $request, array $recieved_data, PDO $pdo_link) {

        if ($request == 'get_serial_number') {
            $this->request_handler = new SerialNumber();
            $this->request_handler->getNewSerialNumber($recieved_data, $pdo_link);
            
        } else if ($request == 'send_testing_info') {
            $this->request_handler = new TestingInfo();
            $this->request_handler->sendTestingInfo($recieved_data, $pdo_link);

        } else if ($request == 'get_testing_info') {
            $this->request_handler = new TestingInfo();
            $this->request_handler->getTestingInfo($recieved_data, $pdo_link);

        } else {
            die(json_encode(['error' => 'unknown request parameter']));
        }
    }
}
?>
