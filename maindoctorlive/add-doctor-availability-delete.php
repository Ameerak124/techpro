<?php
header("Content-Type: application/json; charset=UTF-8");
include "pdo-db.php";
$response = array();
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$transid = trim($data->transid);
$ipaddress = $_SERVER['REMOTE_ADDR'];
$apiurl = substr($_SERVER["SCRIPT_NAME"], strrpos($_SERVER["SCRIPT_NAME"], "/") + 1);
$mybrowser = get_browser(null, true);
try {
    if (!empty($accesskey) && !empty($transid)) {
        //Check access 
        $check = $pdoread->prepare("SELECT `userid`,cost_center FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check->execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        $costcenter = $result['cost_center'];
        if ($check->rowCount() > 0) {
            $gen = $pdo4->prepare("UPDATE `doctor_availability` SET `estatus` = 'Inactive' WHERE `transid`=:transid AND `estatus`='Active'");
            $gen->bindParam(':transid', $transid, PDO::PARAM_STR);
            $gen->execute();
            if ($gen->rowCount() > 0) {
				http_response_code(200);
                $response['error'] = false;
                $response['message'] = "Data Deleted";
            } else {
				http_response_code(503);
                $response['error'] = true;
                $response['message'] = "Data Not Deleted";
            }
        } else {
			http_response_code(400);
            $response['error'] = true;
            $response['message'] = "Access denied! Please try to re-login";
        }
    } else {
		http_response_code(400);
        $response['error'] = true;
        $response['message'] = "Sorry! some details are missing ";
    }
} catch (PDOException $e) {
    http_response_code(503);
    $response['error'] = true;
    $response['message'] = "Connection Failed";
    $errorlog = $pdoread->prepare("INSERT IGNORE INTO `error_logs`(`sno`, `created_by`, `created_on`, `message`, `api_url`) VALUES (NULL,:userid,CURRENT_TIMESTAMP,:errmessage,:apiurl)");
    $errorlog->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
    $errorlog->bindParam(':errmessage', $e->getMessage(), PDO::PARAM_STR);
    $errorlog->bindParam(':apiurl', $apiurl, PDO::PARAM_STR);
    $errorlog->execute();
}
echo json_encode($response);
$pdoread = null;
$pdo4 = null;
?>
