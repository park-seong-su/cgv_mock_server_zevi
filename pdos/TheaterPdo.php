<?php


function theaterListShow($longitude, $latitude)
{
    $pdo = pdoSqlConnect();
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
    $query = "select distinct t.theaterID as theaterID, 
                              t.area as area,  
                              t.name as theater, 
                              t.oldAddress as oldAddress, 
                              t.newAddress as newAddress 
              from Theater t";
    if(isset($_GET['movieID'])) {
        $movieID = $_GET['movieID'];
        $query = $query." join Screen scn on scn.theaterID=t.theaterID
                          join Schedule sch on sch.screenID=scn.screenID
                          join Movie m on sch.movieID = m.movieID
                          where m.movieID=".$movieID;
    }
    $query = $query." order by area ASC, theaterID asc;";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $resCnt = count($res);
    for($i=0; $i<$resCnt; $i++) {
        //거리출력
        $res[$i]['distance'] = new stdClass;
        $distance = getDistance($res[$i]['oldAddress'], $res[$i]['newAddress'], $longitude, $latitude);
        if($distance == "noExistResult" || $distance == "newAddressGeocodingApiError" || $distance == "oldAdrressGeocodingApiError" )
            $res[$i]['distance'] = "none";
        else $res[$i]['distance'] = number_format($distance / 1000, 1)."Km";
        unset($res[$i]['oldAddress']); unset($res[$i]['newAddress']);

        //타입출력
        $query = "select distinct type from Screen where theaterID=\"".$res[$i]['theaterID']."\" order by type asc;";
        $st = $pdo->prepare($query);
        $st->execute();
        $st->setFetchMode(PDO::FETCH_ASSOC);
        $temp = $st->fetchAll();

        $res[$i]['specials'] = new stdClass;
        $tempCnt = count($temp);
        $varNum = 1;
        for($j=0; $j<$tempCnt; $j++) {
            $temp[$j]['type'] = screenTypeDecoding($temp[$j]['type']);
            if($temp[$j]['type'] == "2D") continue;
            $varName = "special".($varNum++);
            $res[$i]['specials']->$varName = $temp[$j]['type'];
        }
        if(empty((array)$res[$i]['specials'])) $res[$i]['specials'] = "none";

        $res[$i]['area'] = areaDecoding($res[$i]['area']);
    }

    $st = null;
    $pdo = null;
    return $res;
}