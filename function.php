<?php
/**
 * Created by PhpStorm.
 * User: Jerry
 * Date: 2016/11/9
 * Time: 上午8:28
 */
require_once "config.php";
$mysqli = new mysqli($db_host, $db_username, $db_password, $db_database);

/**测试输入是否合法
 * @param $target
 * @param $name
 * @param $studentID
 * @param $contact
 * @param $message
 * 如果返回值是 0 则表示输入合法
 */
function checkInput($target, $name, $studentID, $contact, $message)
{
    $state = 0;
    if ($target == "" || $target == " " || mb_strlen($target, 'utf-8') > 50) {
        $state += 1;
    }
    if ($name == "" || $name = " " || mb_strlen($name, 'utf-8') > 50) {
        $state += 2;
    }
    if ($studentID == "" || $studentID == " " || mb_strlen($studentID, 'utf-8')) {
        $state += 4;
    }
    if ($contact == "" || $contact == " " || mb_strlen($contact, 'utf-8') > 50) {
        $state += 8;
    }
    if (mb_strlen($message, 'utf-8') > 300) {
        $state += 16;
    }
    return $state;
}

/**
 * 登记
 */
function signUp()
{
    header("Access-Control-Allow-Origin: " . $GLOBALS['origin_site']);
    if (isset($_POST['target']) && isset($_POST['name']) && isset($_POST['studentID'])
        && isset($_POST['contact']) && isset($_POST['message'])
    ) {
        $target = trim(filter_var($_POST['target']));
        $name = trim(filter_var($_POST['name']));
        $studentID = trim(filter_var($_POST['studentID']));
        $contact = trim(filter_var($_POST['contact']));
        $message = filter_var($_POST['message']);
        $state_code = checkInput($target, $name, $studentID, $contact, $message);
        global $db_table,$mysqli;
        if ($state_code == 0) {
            $sql = "insert into $db_table (target, name, stuId, contact, message) values(?,?,?,?,?)";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param("sssss",$target,$name,$studentID,$contact,$message);
            $stmt->execute();
        } else {
            echo json_encode(array("state" => false, "code" => $state_code));
        }
    } else {
        echo json_encode(array("state" => false, "code" => -1));
    }
}

if (isset($_POST['function'])) {
    if ($_POST['function'] == "signUp") {
        signUp();
    }
} else {
    echo json_encode(array("state" => false, "code" => -2));
}