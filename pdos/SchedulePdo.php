<?php


function scheduleShow($longitude, $latitude)
{
    $pdo = pdoSqlConnect();
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);

    //쿼리스트링 값 저장하기
    $isSetTitle = FALSE;
    $movieID1 = "\"\""; $movieID2 = "\"\"";
    if(isset($_GET['movieID1']) || isset($_GET['movieID2'])) {
        $isSetTitle = TRUE;
        if(isset($_GET['movieID1'])) $movieID1 = $_GET['movieID1'];
        if(isset($_GET['movieID2'])) $movieID2 = $_GET['movieID2'];
    }
    $isSetTheater = FALSE;
    $theaterID1 = "\"\""; $theaterID2 = "\"\""; $theaterID3 = "\"\""; $theaterID4 = "\"\""; $theaterID5 = "\"\"";
    if(isset($_GET['theaterID1']) || isset($_GET['theaterID2']) || isset($_GET['theaterID3']) || isset($_GET['theaterID4']) || isset($_GET['theaterID5'])) {
        $isSetTheater = TRUE;
        if(isset($_GET['theaterID1'])) $theaterID1 = $_GET['theaterID1'];
        if(isset($_GET['theaterID2'])) $theaterID2 = $_GET['theaterID2'];
        if(isset($_GET['theaterID3'])) $theaterID3 = $_GET['theaterID3'];
        if(isset($_GET['theaterID4'])) $theaterID4 = $_GET['theaterID4'];
        if(isset($_GET['theaterID5'])) $theaterID5 = $_GET['theaterID5'];
    }

    //영화관 출력 -> 지오코딩 api로 거리 계산 끝내놓고 상영시간표 출력 한 뒤 영화관과 거리 매칭시키기
    $query = "select distinct t.area as area, 
                              t.theaterID as theaterID, 
                              t.name as theater, 
                              t.oldAddress as oldAddress, 
                              t.newAddress as newAddress 
              from Theater t";
    if($isSetTitle) {
        $query = $query." join Screen scn on scn.theaterID=t.theaterID
                          join Schedule sch on sch.screenID=scn.screenID
                          join Movie m on sch.movieID = m.movieID
                          where m.movieID in (".$movieID1.", ".$movieID2.")";
    }
    if($isSetTheater) {
        if($isSetTitle) $query = $query." or t.theaterID in (".$theaterID1.", ".$theaterID2.", ".$theaterID3.", ".$theaterID4.", ".$theaterID5.")";
        else $query = $query." where t.theaterID in (".$theaterID1.", ".$theaterID2.", ".$theaterID3.", ".$theaterID4.", ".$theaterID5.")";
    }
    $query = $query." order by area ASC, theaterID asc;";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $theaterInfo = $st->fetchAll();

    $theaterInfoCnt = count($theaterInfo);
    for($i=0; $i<$theaterInfoCnt; $i++) {
        //거리출력
        $theaterInfo[$i]['distance'] = new stdClass;
        $distance = getDistance($theaterInfo[$i]['oldAddress'], $theaterInfo[$i]['newAddress'], $longitude, $latitude);
        if ($distance == "noExistResult" || $distance == "newAddressGeocodingApiError" || $distance == "oldAdrressGeocodingApiError")
            $theaterInfo[$i]['distance'] = "none";
        else $theaterInfo[$i]['distance'] = number_format($distance / 1000, 1) . "Km";
        unset($theaterInfo[$i]['oldAddress']); unset($theaterInfo[$i]['newAddress']);
    }


    //상영시간표 출력
    $query = "select sch.scheduleID as scheduleID,
                     t.name as theater,
                     scn.description as screen,
                     scn.type as screenType,
                     scn.capacity as capacity,
                     concat(date_format(sch.startDateTime, '%Y.%c.%e '), '(',substr('일월화수목금토', dayofweek(sch.startDateTime), 1), ')') as date,
                     m.titleKo as title,
                     m.ageLimit as ageLimit,
                     trim(leading '0' from (date_format(sec_to_time(m.playTime*60), '%H시간%i분'))) as playTime,
                     date_format(sch.startDateTime, '%H:%i') as startTime,
                     date_format(date_add(sch.startDateTime, interval (m.playTime+10) minute), '%H:%i') as endTime,
                     sch.count as count
              from Schedule sch
              join Screen scn on scn.screenID=sch.screenID
              join Theater t on t.theaterID=scn.theaterID
              join Movie m on m.movieID=sch.movieID";
    if($isSetTitle) {
        $query = $query." where m.movieID in (".$movieID1.", ".$movieID2.")";
    }
    if($isSetTheater) {
        if($isSetTitle) $query = $query." and t.theaterID in (".$theaterID1.", ".$theaterID2.", ".$theaterID3.", ".$theaterID4.", ".$theaterID5.")";
        else $query = $query." where t.theaterID in (".$theaterID1.", ".$theaterID2.", ".$theaterID3.", ".$theaterID4.", ".$theaterID5.")";
    }
    if(!$isSetTitle && !$isSetTheater) {
        $query = $query." where sch.startDateTime > date_sub(now(), interval 40 minute)";
    }
    else {
        $query = $query." and sch.startDateTime > date_sub(now(), interval 40 minute)";
    }
    $query = $query." order by t.theaterID ASC, sch.startDateTime ASC, scn.screenID ASC;";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $resCnt = count($res);
    for($i=0; $i<$resCnt; $i++) {
        $res[$i]['screenType'] = screenTypeDecoding($res[$i]['screenType']);
        if($res[$i]['screenType'] != "2D") $res[$i]['screenType'] = $res[$i]['screenType']." 2D";
        $res[$i]['ageLimit'] = ageLimitDecoding($res[$i]['ageLimit']);
        $res[$i]['distance'] = new stdClass;
        for($j=0; $j<$theaterInfoCnt; $j++) {
            if($res[$i]['theater'] === $theaterInfo[$j]['theater']) {
                $res[$i]['distance'] = $theaterInfo[$j]['distance'];
                break;
            }
            else $res[$i]['distance'] = "none";
        }
    }

    $st = null;
    $pdo = null;

    if($resCnt == 0) return "none";
    return $res;
}