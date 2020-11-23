<?php
require 'function.php';
require 'EncodingAndDecoding.php';
require 'ValidationFunction.php';
require 'KakaoPayApi.php';
require './pdos/ValidationPdo.php';
require './pdos/ReservationPdo.php';

const JWT_SECRET_KEY = "TEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEYTEST_KEY";

$res = (Object)Array();
header('Content-Type: json');
$req = json_decode(file_get_contents("php://input"));
try {
    addAccessLogs($accessLogs, $req);
    switch ($handler) {
        /*
         * API No. 10
         * API Name : 예매 API
         * 마지막 수정 날짜 : 20.09.07
         */
        case "reserve":
            http_response_code(200);
            $jwt = $_SERVER["HTTP_X_ACCESS_TOKEN"];
            if(!isValidHeader($jwt, JWT_SECRET_KEY)) {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "올바르지 않은 토큰입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidReserveBody($req)) {
                $res->isSuccess = FALSE;
                $res->code = 500;
                $res->message = "body 형식이 맞지 않습니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidScheduleIDLen($req->scheduleID)) {
                $res->isSuccess = FALSE;
                $res->code = 510;
                $res->message = "scheduleID는 21자리를 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isStartScheduleID005($req->scheduleID)) {
                $res->isSuccess = FALSE;
                $res->code = 511;
                $res->message = "scheduleID는 005로 시작해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidScheduleID($req->scheduleID)) {
                $res->isSuccess = FALSE;
                $res->code = 512;
                $res->message = "존재하지 않은 scheduleID입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $seatCnt = count($req->seats);
            for($i=0; $i<$seatCnt; $i++) {
                $check = FALSE;
                if(!isValidSeatIDLen($req->seats[$i]->seatID)) {
                    $check = TRUE;
                    $res->isSuccess = FALSE;
                    $res->code = 520;
                    $res->message = "seatID는 21자리를 입력해주세요.";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }
                if(!isStartSeatID003($req->seats[$i]->seatID)) {
                    $check = TRUE;
                    $res->isSuccess = FALSE;
                    $res->code = 521;
                    $res->message = "seatID는 003으로 시작해주세요.";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }
                if(!isValidSeatID($req->seats[$i]->seatID)) {
                    $check = TRUE;
                    $res->isSuccess = FALSE;
                    $res->code = 522;
                    $res->message = "존재하지 않은 seatID입니다.";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }
                if(!isValidPriceType($req->seats[$i]->priceType)) {
                    $check = TRUE;
                    $res->isSuccess = FALSE;
                    $res->code = 530;
                    $res->message = "priceType은 일반 또는 청소년 또는 우대로 입력해주세요.";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }
                if(!is_numeric($req->seats[$i]->price)) {
                    $check = TRUE;
                    $res->isSuccess = FALSE;
                    $res->code = 540;
                    $res->message = "price는 숫자를 입력해주세요.";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }
            }
            if($check) break;
            if(!is_numeric($req->totalPrice)) {
                $res->isSuccess = FALSE;
                $res->code = 550;
                $res->message = "price는 숫자를 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(!isValidPaymentMethod($req->method)) {
                $res->isSuccess = FALSE;
                $res->code = 560;
                $res->message = "method는 카카오페이를 입력해주세요.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            if(isReservedSeat($req->scheduleID, $req->seats)) {
                $res->isSuccess = FALSE;
                $res->code = 300;
                $res->message = "이미 예매된 좌석입니다.";
                echo json_encode($res, JSON_NUMERIC_CHECK);
                break;
            }
            $userData = getDataByJWToken($jwt, JWT_SECRET_KEY);

            //추후에 method에 따라 분류하면됨 현재 method가 카카오페이라면이라고 생각하고 진행
            if($req->method == "카카오페이") {
                $seatCnt = count($req->seats);
                for($i=0; $i<$seatCnt; $i++) $req->seats[$i]->priceType = priceTypeEncoding($req->seats[$i]->priceType);
                $req->method = paymentMethodEncoding($req->method);
                $reservationInfo = reservationReady($userData->id, $userData->pw, $req->scheduleID, $req->seats, $req->method);
                $reservationInfo->totalPrice = $req->totalPrice;
                $ready = kakaoPayReady($reservationInfo);
                if($ready == "kakaoPayReadyError") {
                    $res->isSuccess = FALSE;
                    $res->code = 400;
                    $res->message = "카카오페이 결제 준비 실패";
                    echo json_encode($res, JSON_NUMERIC_CHECK);
                    break;
                }
                kakaoPayInfoRegister($ready->kakaoPayID, $reservationInfo->reservationID, $ready->tid);
                unset($ready->kakaoPayID); unset($ready->tid);

                $res->result = $ready;
                $res->isSuccess = TRUE;
                $res->code = 100;
                $res->message = "카카오페이 결제 준비 성공";
                echo json_encode($res);
                break;
            }


        case "kakaoPaySuccess":
            $kakaoPayInfo = getKakaoPayInfo($vars["kakaoPayID"]);
            $success = kakaoPaySuccess($kakaoPayInfo->reservationID, $kakaoPayInfo->userID, $kakaoPayInfo->tid);
            if($success == "kakaoPaySuccessError") {
                $res->isSuccess = FALSE;
                $res->code = 200;
                $res->message = "카카오페이 결제 승인 실패";
                echo json_encode($res);
            }
            scheduleCountUpdate($kakaoPayInfo->scheduleID, $kakaoPayInfo->seatCnt); //상영시간표에 count 증가시키기
            kakaoPayAidUpdate($kakaoPayInfo->kakaoPayID, $success->aid); //KakaoPay 테이블에 aid 값과 isCompeleted 1로 update하기
            reservationStateUpdate($kakaoPayInfo->reservationID); //Reservation 테이블의 state도 100(결제완료)로 update하기

            $res->result = "success";
            $res->isSuccess = TRUE;
            $res->code = 100;
            $res->message = "카카오페이 결제 성공";
            echo json_encode($res);
            break;

        case "kakaoPayCancle":
            $res->isSuccess = FALSE;
            $res->code = 300;
            $res->message = "카카오페이 결제 실패";
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
