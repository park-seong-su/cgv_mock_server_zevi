<?php


function genderEncoding($gender) {
    if($gender == "남") return 0;
    else return 1;
}


function ageLimitDecoding($age) {
    if($age == 0) return "전체";
    else if($age == 12) return "12";
    else if($age == 15) return "15";
    else if($age == 20) return "청불";
    else return "ageLimitDecodingError";
}


function nowDecoding($now) {
    if($now == 1) return "yes";
    else if($now == 0) return "no";
    else return "nowDecodingError";
}


function screenTypeDecoding($type) {
    switch($type) {
        case 0: return "2D";
        case 1: return "4DX";
        case 2: return "IMAX";
        case 3: return "STARTIUM";
        case 4: return "PRIMIUM";
        case 5: return "GOLD CLASS";
        case 6: return "CINE de CHEF";
        case 7: return "CINE KIDS";
        case 8: return "브랜드관";
        case 9: return "CGV아트하우스";
        case 10: return "SPHEREX";
        case 11: return "TEMPUR CINEMA";
        case 12: return "SCREENX";
        case 13: return "씨네앤포레";
        case 14: return "SKYBOX";
        case 15: return "씨네앤리빙룸";
        default: return "screenTypeDecodingFuncError";
    }
}


function areaDecoding($area) {
    switch($area) {
        case 1: return "서울";
        case 2: return "경기";
        case 3: return "인천";
        case 4: return "강원";
        case 5: return "대전/충청";
        case 6: return "대구";
        case 7: return "부산/울산";
        case 8: return "경상";
        case 9: return "광주/전라/제주";
        default: return "areaDecodingFuncError";
    }
}


function seatLineDecoding($line) {
    switch($line) {
        case 1: return "A";
        case 2: return "B";
        case 3: return "C";
        case 4: return "D";
        case 5: return "E";
        case 6: return "F";
        case 7: return "G";
        case 8: return "H";
        case 9: return "I";
        case 10: return "G";
        case 11: return "K";
        case 12: return "L";
        case 13: return "M";
        case 14: return "N";
        case 15: return "O";
        case 16: return "P";
        case 17: return "Q";
        case 18: return "R";
        case 19: return "S";
        case 20: return "T";
        case 21: return "U";
        case 22: return "V";
        case 23: return "W";
        case 24: return "X";
        case 25: return "Y";
        case 26: return "Z";
        default: return "seatLineDecodingError";
    }
}


function seatTypeDecoding($seatType) {
    switch($seatType) {
        case 0: return "이코노미석";
        case 1: return "스탠다드석";
        case 2: return "프라임석";
        case 3: return "장애인석";
        case 4: return "sweet box";
        case 5: return "프리미엄석";
        case 6: return "소파";
        case 7: return "리클라이너";
        case 8: return "커플소파";
        default: return "seatTypeDecodingError";
    }
}


function priceTypeEncoding($priceType) {
    switch($priceType) {
        case "일반": return 0;
        case "청소년": return 1;
        case "우대": return 2;
        default: return "priceTypeEncodingError";
    }
}


function paymentMethodEncoding($method) {
    switch($method) {
        case "카카오페이": return 100;
        default : return "paymentMethodEncodingError";
    }
}