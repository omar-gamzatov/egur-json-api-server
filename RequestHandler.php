<?php
require_once("TestingInfo.php");
require_once("SerialNumber.php");

class RequestHandler {
    private $request_handler;


    public function __construct($request, $recieved_data, $pdo_link) {

        if ($request == 'get_serial_number') {
            $this->request_handler = new SerialNumber($recieved_data, $pdo_link);
            $this->request_handler->getNewSerialNumber();
            
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