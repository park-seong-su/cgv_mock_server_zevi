<?php


function reservationReady($id, $pw, $scheduleID, $seats, $method) {
    $pdo = pdoSqlConnect();
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
    //userID 찾기
    $query = "SELECT userID FROM User WHERE id= ? AND pw = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $pw]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    //reservation 업데이트
    $userID = $res[0]['userID'];
    $reservationID = '006'.date('YmdHis').sprintf('%04d',rand(0000,9999));
    $paymentReadyState = 0;
    $seatCnt = count($seats);
    for($i=0; $i<$seatCnt; $i++) {
        $query = "INSERT INTO Reservation
                  (reservationID, userID, scheduleID, seatID, priceType, price, method, state)
                  VALUES
                  (?, ?, ?, ?, ?, ?, ?, ?);";
        $st = $pdo->prepare($query);
        $st->execute([$reservationID, $userID, $scheduleID, $seats[$i]->seatID, $seats[$i]->priceType, $seats[$i]->price, $method, $paymentReadyState]);
    }

    $st = null;
    $pdo = null;

    $reservationInfo = new stdClass;
    $reservationInfo->reservationID = $reservationID;
    $reservationInfo->userID = $userID;
    $reservationInfo->scheduleID = $scheduleID;

    return $reservationInfo;
}


function kakaoPayInfoRegister($kakaoPayID, $reservationID, $tid) {
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO KakaoPay
              (kakaoPayID, reservationID, tid)
              VALUES
              (?, ?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$kakaoPayID, $reservationID, $tid]);
}


function getKakaoPayInfo($kakaoPayID) {
    $pdo = pdoSqlConnect();
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
    $query = "select k.kakaoPayID as kakaoPayID,
                     k.tid as tid,
                     r.reservationID as reservationID,
                     r.userID as userID,
                     r.scheduleID as scheduleID,
                     count(r.seatID) as seatCnt
              from KakaoPay k
              join Reservation r on r.reservationID=k.reservationID
              where kakaoPayID=?;";
    $st = $pdo->prepare($query);
    $st->execute([$kakaoPayID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    return (object)$res[0];
}


function scheduleCountUpdate($schduleID, $seatCnt) {
    $pdo = pdoSqlConnect();
    $query = "update Schedule
              set count=count+?
              where scheduleID=?;";
    $st = $pdo->prepare($query);
    $st->execute([$seatCnt, $schduleID]);
}


function kakaoPayAidUpdate($kakaoPayID, $aid) {
    $pdo = pdoSqlConnect();
    $query = "update KakaoPay
              set aid=?, isCompeleted=1
              where kakaoPayID=?;";
    $st = $pdo->prepare($query);
    $st->execute([$aid, $kakaoPayID]);
}


function reservationStateUpdate($reservationID) {
    $paymentCompleteState = 100;
    $pdo = pdoSqlConnect();
    $query = "update Reservation
              set state=?
              where reservationID=?;";
    $st = $pdo->prepare($query);
    $st->execute([$paymentCompleteState, $reservationID]);
}