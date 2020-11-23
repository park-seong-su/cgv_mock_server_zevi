<?php
require 'function.php';
require 'ValidationFunction.php';
require './pdos/ValidationPdo.php';
require './pdos/ReviewPdo.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        /*
         * API No. 14
         * API Name : 실관람평 조회 API
         * 마지막 수정 날짜 : 20.09.10
         */
        case "reviewListShow":
            http_response_code(200);
            if(!isValidMovieID($vars['movieID'])) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "존재하지 않은 movieID입니다.";
                echo json_encode($res);
                break;
            }
            if(isset($_GET['currentPage'])) {
                if(!is_numeric($_GET['currentPage']) || $_GET['currentPage'] <= 0) {
                    $res->isSuccess = FALSE;
                    $res->code = 300;
                    $res->message = "쿼리스트링 currentPage는 0보다 큰 정수를 입력해주세요.";
                    echo json_encode($res);
                    break;
                }
            }
            $res->result = reviewListShow($vars['movieID']);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "영화 리스트 조회 성공";
            echo json_encode($res);
            break;
        /*
         * API No. 15
         * API Name : 실관람평 작성 API
         * 마지막 수정 날짜 : 20.09.11
         */
        case "reviewRegister":
            http_response_code(200);
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if(!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "올바르지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidMovieID($vars['movieID'])) {
                $res->isSuccess = FALSE;
                $res->code = 300;
                $res->message = "존재하지 않은 movieID입니다.";
                echo json_encode($res);
                break;
            }
            if(!isValidReviewRegisterBody($req)) {
                $res->isSuccess = FALSE;
                $res->code = 500;
                $res->message = "body 형식이 맞지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidCommentLen($req->comment)) {
                $res->isSuccess = FALSE;
                $res->code = 510;
                $res->message = "comment는 10이상 255이하의 길이를 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $userData = getDataByJWToken($jwt, JWT_SECRET_KEY);
            if(!isValidRealAudience($userData->id, $userData->pw, $vars['movieID'])) {
                $res->isSuccess = FALSE;
                $res->code = 400;
                $res->message = "실관람객에 한하여 평점 작성이 가능합니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $res->result = reviewRegister($userData->id, $userData->pw, $vars['movieID'], $req->comment);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "실관람평 작성 성공";
            echo json_encode($res);
            break;
        /*
         * API No. 16
         * API Name : 실관람평 댓글 작성 API
         * 마지막 수정 날짜 : 20.09.11
         */
        case "reviewReplyRegister":
            http_response_code(200);
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if(!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "올바르지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidMovieID($vars['movieID'])) {
                $res->isSuccess = FALSE;
                $res->code = 300;
                $res->message = "존재하지 않은 movieID입니다.";
                echo json_encode($res);
                break;
            }
            if(!isValidReviewReplyRegisterBody($req)) {
                $res->isSuccess = FALSE;
                $res->code = 500;
                $res->message = "body 형식이 맞지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidCommentLen($req->comment)) {
                $res->isSuccess = FALSE;
                $res->code = 510;
                $res->message = "comment는 10이상 255이하의 길이를 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $userData = getDataByJWToken($jwt, JWT_SECRET_KEY);
            if(!isValidReviewID($req->reviewID)) {
                $res->isSuccess = FALSE;
                $res->code = 520;
                $res->message = "존재하지 않은 reviewID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $res->result = reviewReplyRegister($userData->id, $userData->pw, $vars['movieID'], $req->reviewID, $req->comment);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "실관람평 댓글 작성 성공";
            echo json_encode($res);
            break;
        /*
         * API No. 17
         * API Name : 실관람평 하트 토글 API
         * 마지막 수정 날짜 : 20.09.11
         */
        case "reviewHeartToggle":
            http_response_code(200);
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if(!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "올바르지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidMovieID($vars['movieID'])) {
                $res->isSuccess = FALSE;
                $res->code = 300;
                $res->message = "존재하지 않은 movieID입니다.";
                echo json_encode($res);
                break;
            }
            if(!isValidReviewHeartToggleBody($req)) {
                $res->isSuccess = FALSE;
                $res->code = 500;
                $res->message = "body 형식이 맞지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $userData = getDataByJWToken($jwt, JWT_SECRET_KEY);
            if(!isValidReviewID($req->reviewID)) {
                $res->isSuccess = FALSE;
                $res->code = 510;
                $res->message = "존재하지 않은 reviewID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $res->result = reviewHeartToggle($userData->id, $userData->pw, $vars['movieID'], $req->reviewID);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "실관람평 하트 토글 성공";
            echo json_encode($res);
            break;
        /*
         * API No. 18
         * API Name : 실관람평 댓글 하트 토글 API
         * 마지막 수정 날짜 : 20.09.11
         */
        case "reviewReplyHeartToggle":
            http_response_code(200);
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if(!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "올바르지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidMovieID($vars['movieID'])) {
                $res->isSuccess = FALSE;
                $res->code = 300;
                $res->message = "존재하지 않은 movieID입니다.";
                echo json_encode($res);
                break;
            }
            if(!isValidReviewReplyHeartToggleBody($req)) {
                $res->isSuccess = FALSE;
                $res->code = 500;
                $res->message = "body 형식이 맞지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $userData = getDataByJWToken($jwt, JWT_SECRET_KEY);
            if(!isValidReviewReplyID($req->reviewID, $req->seq)) {
                $res->isSuccess = FALSE;
                $res->code = 510;
                $res->message = "존재하지 않은 reviewID, seq입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $res->result = reviewReplyHeartToggle($userData->id, $userData->pw, $vars['movieID'], $req->reviewID, $req->seq);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "실관람평 댓글 하트 토글 성공";
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
