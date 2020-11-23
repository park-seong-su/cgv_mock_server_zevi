<?php


function geocodingApiCall($address, $longitude, $latitude) {
    $clientID = "l31xzyaf5n";
    $clidentSecrete = "sXsvCm84rv5rdmAxhB4iaBcwh96NqqC8tYkKElGn";
    $header = array(
        "X-NCP-APIGW-API-KEY-ID: ".$clientID,
        "X-NCP-APIGW-API-KEY: ".$clidentSecrete
    );

    $url = "https://naveropenapi.apigw.ntruss.com/map-geocode/v2/geocode";
    $queryParams = "?".urlencode("query")."=".urlencode($address);
    $queryParams .= "&".urlencode('coordinate').'='.urlencode($longitude.",".$latitude);
    $queryParams .= "&".urlencode('count').'='.urlencode("1");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url.$queryParams);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, TRUE);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    //curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //푸시할때는 주석처리하기
    //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //푸시할때는 주석처리하기
    $responseStr = curl_exec($ch);
    $statuseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if($statuseCode == 200) {
        $braceStartPos = strpos($responseStr, "{");
        $responseStr = substr($responseStr, $braceStartPos);
        $response = json_decode($responseStr);
        if($response->meta->count == 0) return "responseCountZero";
        else return $response->addresses[0]->distance;
    }
    else {
        return "geocodingApiError";
    }
}


function getDistance($oldAddress, $newAddress, $longitude, $latitude) {
    $distance = geocodingApiCall($oldAddress, $longitude, $latitude);
    if($distance == "responseCountZero") {
        $distance = geocodingApiCall($newAddress, $longitude, $latitude);
        if($distance == "responseCountZero") return "noExistResult";
        else if ($distance == "geocodingApiError") return "newAddressGeocodingApiError";
        else return $distance;
    }
    else if($distance == "geocodingApiError") return "oldAdrressGeocodingApiError";
    else return $distance;
}