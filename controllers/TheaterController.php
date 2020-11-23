<?php
require 'function.php';
require 'EncodingAndDecoding.php';
require 'ValidationFunction.php';
require 'GeocodingApi.php';
require './pdos/ValidationPdo.php';
require './pdos/TheaterPdo.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        /*
         * API No. 5
         * API Name : 극장 리스트 조회 API
         * 마지막 수정 날짜 : 20.09.05
         */
        case "theaterListShow":
            http_response_code(200);
            if(!isValidTheaterListShowBody($req)) {
                $res->isSuccess = FALSE;
                $res->code = 500;
                $res->message = "body 형식이 맞지 않습니다.";
                echo json_encode($res);
                break;
            }
            if(isset($_GET['movieID'])) {
                if(!isValidQueryStringStringType($_GET['movieID'])) {
                    $res->isSuccess = FALSE;
                    $res->code = 200;
                    $res->message = "쿼리스트링 movieID가 올바른 형식이 아닙니다.";
                    echo json_encode($res);
                    break;
                }
                else if(!isValidMovieID(substr($_GET['movieID'], 1, strlen($_GET['movieID']) - 2))) {
                    $res->isSuccess = FALSE;
                    $res->code = 210;
                    $res->message = "존재하지 않은 movieID입니다.";
                    echo json_encode($res);
                    break;
                }
            }
            $res->result = theaterListShow($req->longitude, $req->latitude);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "극장 리스트 조회 성공";
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
