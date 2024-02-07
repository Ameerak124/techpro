<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$response = array();
$ipno = trim($data->ipno);
$accesskey = trim($data->accesskey);
try {


    if (!empty($accesskey) && !empty($ipno)) {
        // Check access
        $check = $pdoread->prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check->execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);

        if ($check->rowCount() > 0) {
            // Logic code start
            $query = $pdoread->prepare("SELECT `userid` AS docid,`username` AS docname,`role` AS department FROM `user_logins` WHERE `role` LIKE '%DMO%' AND `userid` = :userid AND `status` = 'Active' 
            
            UNION ALL 
            SELECT `doc_id` AS docid, `doctor_name` AS docname, `department`
            FROM `doctor_master`
            WHERE `doctor_uid` = :userid
            
            UNION ALL
            
            SELECT `consultantcode` AS docid, `consultantname` AS docname, `department`
            FROM `registration`
            WHERE `admissionno` = :ipno AND `consultantcode` !=:userid
            
            UNION ALL
            
            SELECT `doc_id` AS docid, `doctor_name` AS docname, (
              SELECT `department`
              FROM `doctor_master`
              WHERE `status` = 'Active' AND `doctor_code` = `nursing_doctor_visit`.`doc_id`
            ) AS department
            FROM `nursing_doctor_visit`
            WHERE `ip` = :ipno
            ");
            $query->bindParam(':ipno', $ipno, PDO::PARAM_STR);
            $query->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
            $query->execute();

            if ($query->rowCount() > 0) {
				http_response_code(200);
                $response['error'] = false;
                $response['message'] = "Data found";
                $sn = 0;

                while ($drsearch = $query->fetch(PDO::FETCH_ASSOC)) {
                    $response['list'][] = $drsearch;
                    $sn++;
                }
            } else {
				http_response_code(503);
                $response['error'] = true;
                $response['message'] = "Sorry! No Data Found";
            }
        } else {
			http_response_code(400);
            $response['error'] = true;
            $response['message'] = "Session Access Denied!";
        }
    } else {
		http_response_code(400);
        $response['error'] = true;
        $response['message'] = "Sorry, some details are missing";
    }
} catch (PDOException $e) {
    http_response_code(503);
    $response['error'] = true;
    $response['message'] = "Connection failed";
}
echo json_encode($response);
$pdoread = null;
?>