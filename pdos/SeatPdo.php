<?php


function SeatListShow($scheduleID)
{
    $pdo = pdoSqlConnect();
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
    //좌석 출력
    $query = "select s.seatID as seatID,
                     s.line as line,
                     lpad(s.num, '2', '0') as num,
                     s.type as seatType
              from Seat s
              join Schedule sch on sch.screenID=s.screenID
              where sch.scheduleID=?
              order by s.line asc, s.num asc;";
    $st = $pdo->prepare($query);
    $st->execute([$scheduleID]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $seatInfo = $st->fetchAll();

    //가격 출력
    $query = "select seatID, type, price
              from Price
              where seatID in (";
    $seatInfoCnt = count($seatInfo);
    $seatList = "";
    for($i=0; $i<$seatInfoCnt; $i++) {
        $seatList = $seatList."\"".$seatInfo[$i]['seatID']."\"";
        if($i != $seatInfoCnt - 1) $seatList = $seatList.", ";
        else $seatList = $seatList.")";
    }
    $query = $query.$seatList;
    $query = $query." order by seatID asc, type asc;";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $priceInfo = $st->fetchAll();

    //이미 예매된 좌석인지 출력
    $paymentCompleteState = 100;
    $query = "select seatID, state
              from Reservation
              where scheduleID=? and seatID in (";
    $query = $query.$seatList;
    $query = $query." and state=? order by seatID asc;";
    $st = $pdo->prepare($query);
    $st->execute([$scheduleID, $paymentCompleteState]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $reservationInfo = $st->fetchAll();

    //정리
    for($i=0; $i<$seatInfoCnt; $i++) {
        $seatInfo[$i]['line'] = seatLineDecoding($seatInfo[$i]['line']);
        $seatInfo[$i]['seatType'] = seatTypeDecoding($seatInfo[$i]['seatType']);
        //가격 타입 붙이기 (일반, 청소년, 우대)
        $seatInfo[$i]['priceType'] = new stdClass;
        $priceInfoCnt = count($priceInfo);
        for($j=0; $j<$priceInfoCnt; $j++) {
            if($seatInfo[$i]['seatID'] == $priceInfo[$j]['seatID']) {
                switch($priceInfo[$j]['type']) {
                    case 0: $seatInfo[$i]['priceType']->general = $priceInfo[$j]['price']; break;
                    case 1: $seatInfo[$i]['priceType']->youth = $priceInfo[$j]['price']; break;
                    case 2: $seatInfo[$i]['priceType']->udae = $priceInfo[$j]['price']; break;
                    default: $seatInfo[$i]['priceType'] = "priceTypeError"; break;
                }
            }
        }
        //이미 예매된 좌석인지 붙이기
        $seatInfo[$i]['isEmpty'] = TRUE;
        $reservationInfoCnt = count($reservationInfo);
        for($k=0; $k<$reservationInfoCnt; $k++) {
            if($seatInfo[$i]['seatID'] == $reservationInfo[$k]['seatID']) {
                if($reservationInfo[$k]['state'] == $paymentCompleteState) {
                    $seatInfo[$i]['isEmpty'] = FALSE;
                }
            }
        }
    }

    $st = null;
    $pdo = null;

    return $seatInfo;
}