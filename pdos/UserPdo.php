<?php


function userJoin($id, $pw, $name, $phone, $email, $gender, $age) {
    $pdo = pdoSqlConnect();
    $query = "INSERT INTO User
              (userID, id, pw, name, phone, email, gender, age)
              VALUES
              (CONCAT(\"000\", DATE_FORMAT(NOW(), \"%Y%m%d%H%i%s\"), FLOOR(1000+RAND()*8999)), ?, ?, ?, ?, ?, ?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$id, $pw, $name, $phone, $email, $gender, $age]);
    $st = null;
    $pdo = null;
    return "success";
}


function profileRegister($id, $pw, $image) {
    $pdo = pdoSqlConnect();
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
    //userID 찾기
    $query = "SELECT userID FROM User WHERE id= ? AND pw = ?;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $pw]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();

    $userID = $res[0]['userID'];
    $query = "INSERT INTO Profile
              (profileID, userID, image)
              VALUES
              (CONCAT(\"010\", DATE_FORMAT(NOW(), \"%Y%m%d%H%i%s\"), FLOOR(1000+RAND()*8999)), ?, ?);";
    $st = $pdo->prepare($query);
    $st->execute([$userID, $image]);
    $st = null;
    $pdo = null;
    return "success";
}


function userNameShow($id, $pw) {
    $pdo = pdoSqlConnect();
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $pdo->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
    $query = "select userID as userID, name as userName from User where id=? and pw=?;";
    $st = $pdo->prepare($query);
    $st->execute([$id, $pw]);
    $st->setFetchMode(PDO::FETCH_ASSOC);
    $res = $st->fetchAll();
    $st = null;
    $pdo = null;
    return $res;
}