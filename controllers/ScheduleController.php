<?php
require 'function.php';
require 'EncodingAndDecoding.php';
require 'ValidationFunction.php';
require 'GeocodingApi.php';
require './pdos/ValidationPdo.php';
require './pdos/SchedulePdo.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        /*
         * API No. 8
         * API Name : 상영시간표 조회 API
         * 마지막 수정 날짜 : 20.09.05
         */
        case "scheduleShow":
            http_response_code(200);
            if(!isValidScheduleShowBody($req)) {
                $res->isSuccess = FALSE;
                $res->code = 500;
                $res->message = "body 형식이 맞지 않습니다.";
                echo json_encode($res);
                break;
            }
            if(isset($_GET['movieID1'])) {
                if(!isValidQueryStringStringType($_GET['movieID1'])) {
                    $res->isSuccess = FALSE;
                    $res->code = 210;
                    $res->message = "쿼리스트링 movieID1이 올바른 형식이 아닙니다.";
                    echo json_encode($res);
                    break;
                }
                else if(!isValidMovieID(substr($_GET['movieID1'], 1, strlen($_GET['movieID1']) - 2))) {
                    $res->isSuccess = FALSE;
                    $res->code = 211;
                    $res->message = "존재하지 않은 movieID1입니다.";
                    echo json_encode($res);
                    break;
                }
            }
            if(isset($_GET['movieID2'])) {
                if(!isValidQueryStringStringType($_GET['movieID2'])) {
                    $res->isSuccess = FALSE;
                    $res->code = 220;
                    $res->message = "쿼리스트링 movieID2이 올바른 형식이 아닙니다.";
                    echo json_encode($res);
                    break;
                }
                else if(!isValidMovieID(substr($_GET['movieID2'], 1, strlen($_GET['movieID2']) - 2))) {
                    $res->isSuccess = FALSE;
                    $res->code = 221;
                    $res->message = "존재하지 않은 movieID2입니다.";
                    echo json_encode($res);
                    break;
                }
            }
            if(isset($_GET['theaterID1'])) {
                if(!isValidQueryStringStringType($_GET['theaterID1'])) {
                    $res->isSuccess = FALSE;
                    $res->code = 310;
                    $res->message = "쿼리스트링 theaterID1이 올바른 형식이 아닙니다.";
                    echo json_encode($res);
                    break;
                }
                else if(!isValidTheaterID(substr($_GET['theaterID1'], 1, strlen($_GET['theaterID1']) - 2))) {
                    $res->isSuccess = FALSE;
                    $res->code = 311;
                    $res->message = "존재하지 않은 theaterID1입니다.";
                    echo json_encode($res);
                    break;
                }
            }
            if(isset($_GET['theaterID2'])) {
                if(!isValidQueryStringStringType($_GET['theaterID2'])) {
                    $res->isSuccess = FALSE;
                    $res->code = 320;
                    $res->message = "쿼리스트링 theaterID2이 올바른 형식이 아닙니다.";
                    echo json_encode($res);
                    break;
                }
                else if(!isValidTheaterID(substr($_GET['theaterID2'], 1, strlen($_GET['theaterID2']) - 2))) {
                    $res->isSuccess = FALSE;
                    $res->code = 321;
                    $res->message = "존재하지 않은 theaterID2입니다.";
                    echo json_encode($res);
                    break;
                }
            }
            if(isset($_GET['theaterID3'])) {
                if(!isValidQueryStringStringType($_GET['theaterID3'])) {
                    $res->isSuccess = FALSE;
                    $res->code = 330;
                    $res->message = "쿼리스트링 theaterID3이 올바른 형식이 아닙니다.";
                    echo json_encode($res);
                    break;
                }
                else if(!isValidTheaterID(substr($_GET['theaterID3'], 1, strlen($_GET['theaterID3']) - 2))) {
                    $res->isSuccess = FALSE;
                    $res->code = 331;
                    $res->message = "존재하지 않은 theaterID3입니다.";
                    echo json_encode($res);
                    break;
                }
            }
            if(isset($_GET['theaterID4'])) {
                if(!isValidQueryStringStringType($_GET['theaterID4'])) {
                    $res->isSuccess = FALSE;
                    $res->code = 340;
                    $res->message = "쿼리스트링 theaterID4이 올바른 형식이 아닙니다.";
                    echo json_encode($res);
                    break;
                }
                else if(!isValidTheaterID(substr($_GET['theaterID4'], 1, strlen($_GET['theaterID4']) - 2))) {
                    $res->isSuccess = FALSE;
                    $res->code = 341;
                    $res->message = "존재하지 않은 theaterID4입니다.";
                    echo json_encode($res);
                    break;
                }
            }
            if(isset($_GET['theaterID5'])) {
                if(!isValidQueryStringStringType($_GET['theaterID5'])) {
                    $res->isSuccess = FALSE;
                    $res->code = 350;
                    $res->message = "쿼리스트링 theaterID5이 올바른 형식이 아닙니다.";
                    echo json_encode($res);
                    break;
                }
                else if(!isValidTheaterID(substr($_GET['theaterID5'], 1, strlen($_GET['theaterID5']) - 2))) {
                    $res->isSuccess = FALSE;
                    $res->code = 351;
                    $res->message = "존재하지 않은 theaterID5입니다.";
                    echo json_encode($res);
                    break;
                }
            }
            $res->result = scheduleShow($req->longitude, $req->latitude);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "상영시간표 조회 성공";
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
