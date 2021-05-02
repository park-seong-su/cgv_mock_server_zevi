<?php


function request_curl($url, $is_post=0, $data=array(), $custom_header=null) {
    $ch = curl_init();
    curl_setopt ($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $custom_header);
    if($is_post == 1) curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //푸시할때는 주석처리
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //푸시할때는 주석처리

    $result[0] = curl_exec ($ch);
    $result[1] = curl_errno($ch);
    $result[2] = curl_error($ch);
    $result[3] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close ($ch);
    return $result;
}


// 결제창 호출
function kakaoPayReady($reservationInfo) {
    //$http_host = 'http://localhost/reservation'; //로컬 서버
    $http_host = 'https://dev-api.cgvmock.site/reservation'; //개발 서버
    //$http_host = 'https://api.cgvmock.site/reservation'; //실제 서버
    
    $adminkey = $adminkey; //admin 키
    $cid = $cid; //임시 cid

    $req_auth   = 'Authorization: KakaoAK '.$adminkey;
    $req_cont   = 'Content-type: application/x-www-form-urlencoded;charset=utf-8';
    $kakao_header = array( $req_auth, $req_cont );

    $kakaoPayID = '011'.date('YmdHis').sprintf('%04d',rand(0000,9999));
    $approval_url = $http_host."/kakaoPaySuccess/".$kakaoPayID;
    $cancel_url = $http_host."/kakaoPayCancle";
    $fail_url = $http_host."/kakaoPayCancle";

    $kakao_params = array(
        'cid'               => $cid,                                    // 가맹점코드 10자
        'partner_order_id'  => $reservationInfo->reservationID,       // 예매번호
        'partner_user_id'   => $reservationInfo->userID,              // 유저 id
        'item_name'         => $reservationInfo->scheduleID,            // 상품명(scheduleID)
        'quantity'          => '1',                                     // 상품 수량
        'total_amount'      => $reservationInfo->totalPrice,               // 상품 총액
        'tax_free_amount'   => '0',                                     // 상품 비과세 금액
        'approval_url'      => $approval_url,                           // 결제성공시 콜백url 최대 255자
        'cancel_url'        => $cancel_url,
        'fail_url'          => $fail_url,
    );

    $ready = request_curl('https://kapi.kakao.com/v1/payment/ready', 1, http_build_query($kakao_params), $kakao_header);
    if( $ready[3] != '200' ) return "kakaoPayReadyError";
    $braceStartPos = strpos($ready[0], "{");
    $ready = json_decode(substr($ready[0], $braceStartPos)); unset($ready->tms_result);
    $ready->kakaoPayID = $kakaoPayID;

    return $ready;
}


function kakaoPaySuccess($reservationID, $userID, $tid) {
    $adminkey = $adminkey; //admin 키
    $cid = $cid; //임시 cid

    $req_auth   = 'Authorization: KakaoAK '.$adminkey;
    $req_cont   = 'Content-type: application/x-www-form-urlencoded;charset=utf-8';
    $kakao_header = array( $req_auth, $req_cont );

    $kakao_params = array(
        'cid'               => $cid,                 // 가맹점코드 10자
        'tid'               => $tid,                 // 결제 고유번호. 결제준비 API의 응답에서 얻을 수 있음
        'partner_order_id'  => $reservationID,       // 가맹점 주문번호. 결제준비 API에서 요청한 값과 일치해야 함
        'partner_user_id'   => $userID,              // 가맹점 회원 id. 결제준비 API에서 요청한 값과 일치해야 함
        'pg_token'          => $_GET['pg_token']     // 결제승인 요청을 인증하는 토큰. 사용자가 결제수단 선택 완료시 approval_url로 redirection해줄 때 pg_token을 query string으로 넘겨줌
    );

    $success = request_curl('https://kapi.kakao.com/v1/payment/approve', 1, http_build_query($kakao_params), $kakao_header);
    if( $success[3] != '200' ) return "kakaoPaySuccessError";
    $braceStartPos = strpos($success[0], "{");
    $success = json_decode(substr($success[0], $braceStartPos));

    return $success;
}
