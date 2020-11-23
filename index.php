<?php 
require './pdos/DatabasePdo.php';
require './vendor/autoload.php';

use \Monolog\Logger as Logger;
use Monolog\Handler\StreamHandler;

date_default_timezone_set('Asia/Seoul');
ini_set('default_charset', 'utf8mb4');

//에러출력하게 하는 코드
//error_reporting(E_ALL); ini_set("display_errors", 1);

//Main Server API
$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
    /* ******************   Test   ****************** */
    $r->addRoute('GET', '/', ['IndexController', 'index']);
    $r->addRoute('GET', '/test', ['IndexController', 'test']);
    $r->addRoute('GET', '/test/{testNo}', ['IndexController', 'testDetail']);
    $r->addRoute('POST', '/test', ['IndexController', 'testPost']);
    $r->addRoute('GET', '/jwt', ['MainController', 'validateJwt']);
    $r->addRoute('POST', '/jwt', ['MainController', 'createJwt']);


    /* ******************   CGV   ****************** */
    $r->addRoute('POST', '/auth', ['AuthController', 'authNumRequest']);
    $r->addRoute('POST', '/auth/num', ['AuthController', 'authNumCheck']);
    $r->addRoute('POST', '/user', ['UserController', 'userJoin']);
    $r->addRoute('POST', '/login', ['UserController', 'login']);
    $r->addRoute('POST', '/login/auto', ['UserController', 'autoLogin']);
    $r->addRoute('GET', '/movies', ['MovieController', 'movieListShow']);
    $r->addRoute('POST', '/theaters', ['TheaterController', 'theaterListShow']);
    $r->addRoute('POST', '/schedule', ['ScheduleController', 'scheduleShow']);
    $r->addRoute('GET', '/schedule/{scheduleID}/seats', ['SeatController', 'SeatListShow']);
    $r->addRoute('POST', '/reservation', ['ReservationController', 'reserve']);
    $r->addRoute('GET', '/reservation/kakaoPaySuccess/{kakaoPayID}', ['ReservationController', 'kakaoPaySuccess']);
    $r->addRoute('GET', '/reservation/kakaoPayCancle', ['ReservationController', 'kakaoPayCancle']);
    $r->addRoute('POST', '/profile', ['UserController', 'profileRegister']);
    $r->addRoute('GET', '/movie/{movieID}/review', ['ReviewController', 'reviewListShow']);
    $r->addRoute('POST', '/movie/{movieID}/review', ['ReviewController', 'reviewRegister']);
    $r->addRoute('POST', '/movie/{movieID}/review-reply', ['ReviewController', 'reviewReplyRegister']);
    $r->addRoute('POST', '/movie/{movieID}/review-heart', ['ReviewController', 'reviewHeartToggle']);
    $r->addRoute('POST', '/movie/{movieID}/review-reply-heart', ['ReviewController', 'reviewReplyHeartToggle']);
    $r->addRoute('GET', '/username', ['UserController', 'userNameShow']);


    /* ********************************************* */
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// 로거 채널 생성
$accessLogs = new Logger('ACCESS_LOGS');
$errorLogs = new Logger('ERROR_LOGS');
// log/your.log 파일에 로그 생성. 로그 레벨은 Info
$accessLogs->pushHandler(new StreamHandler('logs/access.log', Logger::INFO));
$errorLogs->pushHandler(new StreamHandler('logs/errors.log', Logger::ERROR));
// add records to the log
//$log->addInfo('Info log');
// Debug 는 Info 레벨보다 낮으므로 아래 로그는 출력되지 않음
//$log->addDebug('Debug log');
//$log->addError('Error log');

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        echo "404 Not Found";
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        echo "405 Method Not Allowed";
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        switch ($routeInfo[1][0]) {
            case 'IndexController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/IndexController.php';
                break;
            case 'MainController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/MainController.php';
                break;
            case 'AuthController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/AuthController.php';
                break;
            case 'UserController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/UserController.php';
                break;
            case 'MovieController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/MovieController.php';
                break;
            case 'TheaterController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/TheaterController.php';
                break;
            case 'ScheduleController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/ScheduleController.php';
                break;
            case 'SeatController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/SeatController.php';
                break;
            case 'ReservationController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/ReservationController.php';
                break;
            case 'ReviewController':
                $handler = $routeInfo[1][1];
                $vars = $routeInfo[2];
                require './controllers/ReviewController.php';
                break;

        }

        break;
}
