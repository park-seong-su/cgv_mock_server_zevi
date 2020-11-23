<?php
require 'function.php';
require 'SensApi.php';
require 'ValidationFunction.php';
require './pdos/AuthPdo.php';
require './pdos/ValidationPdo.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        /*
         * API No. 1
         * API Name : 인증번호 요청 API
         * 마지막 수정 날짜 : 20.09.02
         */
        case "authNumRequest":
            http_response_code(200);
            if(!isValidAuthNumRequestBody($req)) {
                $res->isSuccess = FALSE;
                $res->code = 500;
                $res->message = "body 형식이 맞지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidPhoneLen($req->phone)) {
                $res->isSuccess = FALSE;
                $res->code = 530;
                $res->message = "핸드폰 번호는 11자리를 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isStartPhoneNum010($req->phone)) {
                $res->isSuccess = FALSE;
                $res->code = 531;
                $res->message = "핸드폰 번호는 010으로 시작해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidAgree($req->phoneAgree, $req->idenAgree, $req->telAgree, $req->indiAgree)) {
                $res->isSuccess = FALSE;
                $res->code = 560;
                $res->message = "필수체크가 모두 선택되지 않았습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $authNum = sprintf('%06d',rand(000000,999999)); //랜덤 숫자 생성
            sendMessage($req->phone, $authNum); //메시지 송신
            $res->result = saveAuthNum($req->phone, $authNum);//db에 저장
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "인증번호 요청 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 2
         * API Name : 인증번호 확인 API
         * 마지막 수정 날짜 : 20.09.02
         */
        case "authNumCheck":
            http_response_code(200);
            if(!isValidAuthNumCheckBody($req)) {
                $res->isSuccess = FALSE;
                $res->code = 500;
                $res->message = "body 형식이 맞지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidPhoneLen($req->phone)) {
                $res->isSuccess = FALSE;
                $res->code = 530;
                $res->message = "핸드폰 번호는 11자리를 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isStartPhoneNum010($req->phone)) {
                $res->isSuccess = FALSE;
                $res->code = 531;
                $res->message = "핸드폰 번호는 010으로 시작해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            //인증번호 체크
            if(!isValidAuthNum($req->phone, $req->authNum)) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "입력하신 인증번호가 정확하지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $res->result = phoneCertify($req->phone, $req->authNum);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "본인인증 완료";
            echo json_encode($res, JSON_NUMERIC_CHECK);
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
