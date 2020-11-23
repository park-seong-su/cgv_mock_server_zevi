<?php
require 'function.php';
require 'EncodingAndDecoding.php';
require 'ValidationFunction.php';
require './pdos/ValidationPdo.php';
require './pdos/SeatPdo.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        /*
         * API No. 9
         * API Name : 좌석 조회 API
         * 마지막 수정 날짜 : 20.09.07
         */
        case "SeatListShow":
            http_response_code(200);
            if(!isValidScheduleID($vars["scheduleID"])) {
                $res->isSuccess = TRUE;
                $res->code = 200;
                $res->message = "존재하지 않은 scheduleID입니다.";
                echo json_encode($res);
                break;
            }
            $res->result = SeatListShow($vars["scheduleID"]);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "좌석 조회 성공";
            echo json_encode($res);
            break;

        case "ACCESS_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/access.log");
            break;
        case "ERROR_LOGS":
            //            header('content-type text/html charset=utf-8');
            header('Content-Type: text/html; charset=UTF-8');
            getLogs("./logs/errors.log");
            break;
    }
} catch (\Exception $e) {
    return getSQLErrorException($errorLogs, $e, $req);
}
