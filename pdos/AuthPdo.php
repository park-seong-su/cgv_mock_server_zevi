<?php


function saveAuthNum($phone, $authNum) {
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO Auth
              (authID, phone, num)
              VALUES
              (CONCAT(\"008\", DATE_FORMAT(NOW(), \"%Y%m%d%H%i%s\"), FLOOR(1000+RAND()*8999)), ?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$phone, $authNum]);
    $st = null;
    $pdo = null;
    return "success";
}


function phoneCertify($phone, $authNum) {
    $pdo = pdoSqlConnect();
    $query = "update Auth
              set isCertified=1
              where phone=? and num=?;";
    $st = $pdo->prepare($query);
    $st->execute([$phone, $authNum]);
    $st = null;
    $pdo = null;
    return "success";
}