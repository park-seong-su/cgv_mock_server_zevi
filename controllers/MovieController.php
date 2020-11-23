<?php
require 'function.php';
require 'EncodingAndDecoding.php';
require './pdos/MoviePdo.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        /*
         * API No. 3
         * API Name : 영화 리스트 조회 API
         * 마지막 수정 날짜 : 20.09.01
         */
        case "movieListShow":
            http_response_code(200);
            $res->result = movieListShow();
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "영화 리스트 조회 성공";
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
