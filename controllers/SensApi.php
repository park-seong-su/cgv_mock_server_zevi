<?php


function sendMessage($phone, $authNum) {
    $sID = $sID; // 서비스 ID
    $smsURL = "https://sens.apigw.ntruss.com/sms/v2/services/".$sID."/messages";
    $smsUri = "/sms/v2/services/".$sID."/messages";
    $sKey = $sKey

    $accKeyId = $accKeyId;
    $accSecKey = $accSecKey;

    $sTime = floor(microtime(true) * 1000);

// The data to send to the API
    $postData = array(
        'type' => 'SMS',
        'countryCode' => '82',
        'from' => '01012345678', // 발신번호 (등록되어있어야함)
        'contentType' => 'COMM',
        'content' => "[한국모바일인증(주)]본인확인 인증번호[".$authNum."]입니다. \"타인 노출 금지\"",
        'messages' => array(array('content' => "[한국모바일인증(주)]본인확인 인증번호[".$authNum."]입니다. \"타인 노출 금지\"", 'to' => $phone))
    );

    $postFields = json_encode($postData);

    $hashString = "POST {$smsUri}\n{$sTime}\n{$accKeyId}";
    $dHash = base64_encode( hash_hmac('sha256', $hashString, $accSecKey, true) );

    $header = array(
        // "accept: application/json",
        'Content-Type: application/json; charset=utf-8',
        'x-ncp-apigw-timestamp: '.$sTime,
        "x-ncp-iam-access-key: ".$accKeyId,
        "x-ncp-apigw-signature-v2: ".$dHash
    );

// Setup cURL
    $ch = curl_init($smsURL);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //푸시할때는 주석처리
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //푸시할때는 주석처리

    $response = curl_exec($ch);

    return "success";
}
