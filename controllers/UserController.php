<?php
require 'function.php';
require 'ValidationFunction.php';
require 'EncodingAndDecoding.php';
require './pdos/ValidationPdo.php';
require './pdos/UserPdo.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        /*
         * API No. 3
         * API Name : 회원가입 API
         * 마지막 수정 날짜 : 20.08.31
         */
        case "userJoin":
            http_response_code(200);
            if(!isValidUserJoinBody($req)) {
                $res->isSuccess = FALSE;
                $res->code = 500;
                $res->message = "body 형식이 맞지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(isExistId($req->id)) {
                $res->isSuccess = FALSE;
                $res->code = 510;
                $res->message = "이미 존재하는 id입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidIdLen($req->id)) {
                $res->isSuccess = FALSE;
                $res->code = 511;
                $res->message = "id는 10이상 20이하의 길이를 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidIdFormat($req->id)) {
                $res->isSuccess = FALSE;
                $res->code = 512;
                $res->message = "id는 공백없이 문자, 숫자를 혼합해서 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidPwLen($req->pw)) {
                $res->isSuccess = FALSE;
                $res->code = 520;
                $res->message = "pw는 10이상 20이하의 길이를 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidPwFormat($req->pw)) {
                $res->isSuccess = FALSE;
                $res->code = 521;
                $res->message = "pw는 공백없이 문자, 숫자, 특수문자를 혼합해서 입력해주세요.";
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
            if(!isValidAuthNumLen($req->authNum)) {
                $res->isSuccess = FALSE;
                $res->code = 580;
                $res->message = "authNum은 6자리를 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isCertifiedPhone($req->phone, $req->authNum)) {
                $res->isSuccess = FALSE;
                $res->code = 532;
                $res->message = "인증되지 않은 핸드폰 번호입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidEmailLen($req->email)) {
                $res->isSuccess = FALSE;
                $res->code = 540;
                $res->message = "email은 20자리 이하의 길이를 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidEmailFormat($req->email)) {
                $res->isSuccess = FALSE;
                $res->code = 541;
                $res->message = "email 형식이 맞지않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidGender($req->gender)) {
                $res->isSuccess = FALSE;
                $res->code = 550;
                $res->message = "성별은 남 또는 여로 입력해주세요.";
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
            if(!isValidNameLen($req->name)) {
                $res->isSuccess = FALSE;
                $res->code = 570;
                $res->message = "name은 10자리 이하의 길이를 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $gender = genderEncoding($req->gender);
            $res->result = userJoin($req->id, $req->pw, $req->name, $req->phone, $req->email, $gender, $req->age);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "회원가입 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 4
         * API Name : 로그인 API
         * 마지막 수정 날짜 : 20.08.31
         */
        case "login":
            http_response_code(200);
            if(!isValidLoginBody($req)) {
                $res->isSuccess = FALSE;
                $res->code = 500;
                $res->message = "body 형식이 맞지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidIdLen($req->id)) {
                $res->isSuccess = FALSE;
                $res->code = 511;
                $res->message = "id는 10이상 20이하의 길이를 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidIdFormat($req->id)) {
                $res->isSuccess = FALSE;
                $res->code = 512;
                $res->message = "id는 공백없이 문자, 숫자를 혼합해서 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidPwLen($req->pw)) {
                $res->isSuccess = FALSE;
                $res->code = 520;
                $res->message = "pw는 10이상 20이하의 길이를 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidPwFormat($req->pw)) {
                $res->isSuccess = FALSE;
                $res->code = 521;
                $res->message = "pw는 공백없이 문자, 숫자, 특수문자를 혼합해서 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidUser($req->id, $req->pw)) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "존재하지 않는 사용자입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $jwt = getJWToken($req->id, $req->pw, JWT_SECRET_KEY);
            $res->result = new stdClass();
            $res->result->jwt = $jwt;
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "로그인 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 5
         * API Name : 자동 로그인 API
         * 마지막 수정 날짜 : 20.09.04
         */
        case "autoLogin":
            http_response_code(200);
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if(!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "올바르지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $res->result = "success";
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "로그인 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 11
         * API Name : 프로필 등록 API
         * 마지막 수정 날짜 : 20.09.07
         */
        case "profileRegister":
            http_response_code(200);
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if(!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "올바르지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidProfileRegisterBody($req)) {
                $res->isSuccess = FALSE;
                $res->code = 500;
                $res->message = "body 형식이 맞지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $userData = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $res->result = profileRegister($userData->id, $userData->pw, $req->image);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "프로필 등록 성공";
            echo json_encode($res, JSON_NUMERIC_CHECK);
            break;
        /*
         * API No. 19
         * API Name : 유저네임 조회 API
         * 마지막 수정 날짜 : 20.09.10
         */
        case "userNameShow":
            http_response_code(200);
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if(!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "올바르지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $userData = getDataByJWToken($jwt, JWT_SECRET_KEY);
            $res->result = userNameShow($userData->id, $userData->pw);
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "유저네임 조회 성공";
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
