<?php


function movieListShow()
{
    $pdo = pdoSqlConnect();
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
    $query = "select ifnull(sum(todayAud), 0) as todayAudSum from Movie;";
    $st = $pdo->prepare($query);
    $st->execute();
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    $todayAudSum = $res[0]['todayAudSum'];

    $pdo = pdoSqlConnect();
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
    $query = "select m.movieID as movieID, m.titleKo as titleKo, ifnull(m.titleEn, \"none\") as titleEn,
                     m.ageLimit as ageLimit, m.openDate as openDate,
                     ifnull(concat(truncate(m.todayAud*100/?, 1), \"%\"), \"none\") as bookingRate,
                     truncate(m.todayAud*100/?, 1) as br,
                     m.totalAud as totalAud, m.todayAud as todayAud, m.now as now,
                     ifnull(p.image, \"none\") as image
              from Movie m
              left join Poster p on m.movieID=p.movieID
              order by br desc;";
    $st = $pdo->prepare($query);
    $st->execute([$todayAudSum, $todayAudSum]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $resSize = count($res);
    for($i=0; $i<$resSize; $i++) {
        $res[$i]['ageLimit'] = ageLimitDecoding($res[$i]['ageLimit']);
        $res[$i]['openDate'] = substr($res[$i]['openDate'], 0, 4).".".substr($res[$i]['openDate'], 4, 2).".".substr($res[$i]['openDate'], 6, 2);
        if($res[$i]['totalAud'] === null) $res[$i]['totalAud'] = "none";
        if($res[$i]['todayAud'] === null) $res[$i]['todayAud'] = "none";
        if($res[$i]['bookingRate'] === null) $res[$i]['bookingRate'] = "none";
        unset($res[$i]['br']);
        $res[$i]['now'] = nowDecoding($res[$i]['now']);
    }
    $st = null;
    $pdo = null;
    return $res;
}