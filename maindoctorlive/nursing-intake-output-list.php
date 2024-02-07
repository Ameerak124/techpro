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
$accesskey = trim($data->accesskey);
$ip = trim($data->ip);
$fdate = date('Y-m-d', strtotime($data->fdate));
$tdate = date('Y-m-d', strtotime($data->tdate));

try {
    
    if (!empty($accesskey) && !empty($ip)) {
        $check = $pdoread->prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check->execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if ($check->rowCount() > 0) {

            // date wise list
            $stmt = $pdoread->prepare("SELECT @a:=@a+1 AS sno, `date`, `time`, (case when `category` = 'In Take' then `desp` else '' end) as intake_desp, (case when `category` = 'In Take' then `amount` else '00.00' end) as intake_amount, (case when `category` = 'In Take' then `remarks` else '' end) as intake_rem, (case when `category` = 'Output' then `desp` else '' end) as output_desp, (case when `category` = 'Output' then `amount` else '00.00' end) as output_amount, (case when `category` = 'Output' then `remarks` else '' end) as output_rem, (case when `category` = 'Kilo calories' then `desp` else '' end) as kilo_desp, (case when `category` = 'Kilo calories' then `amount` else '00.00' end) as kilo_amount, (case when `category` = 'Kilo calories' then `remarks` else '' end) as kilo_rem,`category` FROM (SELECT @a:=0) AS a, `nursing_intake_output` WHERE `ip` = :ip  AND DATE(`date`) BETWEEN :fdate AND :tdate  ORDER BY `date` ASC");
			
            $stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
            $stmt->bindParam(':fdate', $fdate, PDO::PARAM_STR);
            $stmt->bindParam(':tdate', $tdate, PDO::PARAM_STR);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
				http_response_code(200);
                $response['error'] = false;
                $response['message'] = "Data Found";
                while ($results = $stmt->fetch(PDO::FETCH_ASSOC)) {

                    $response['intakeoutputlist'][] = $results;
                }
                //list
                $get_count = $pdoread->prepare("SELECT IFNULL((SELECT ROUND(SUM(`amount`),2) FROM nursing_intake_output WHERE `estatus`='Active' AND `category`='In Take' AND `ip`=:ip),'0')AS in_take,IFNULL((SELECT ROUND(SUM(`amount`),2) FROM nursing_intake_output WHERE `estatus`='Active' AND `category`='Output' AND `ip`=:ip ),'0')AS Output, IFNULL((SELECT ROUND(SUM(`amount`),2) FROM nursing_intake_output WHERE `estatus`='Active' AND `category`='Kilo calories' AND `ip`=:ip),'0')AS Kilo FROM `nursing_intake_output` WHERE `ip`=:ip AND DATE(`date`) BETWEEN :fdate AND :tdate ");
                $get_count->bindParam(':ip', $ip, PDO::PARAM_STR);
                $get_count->bindParam(':fdate', $fdate, PDO::PARAM_STR);
                $get_count->bindParam(':tdate', $tdate, PDO::PARAM_STR);
                $get_count->execute();
                $list_res = $get_count->fetch(PDO::FETCH_ASSOC);
                $response['sublist'][] = $list_res;
            } else {
				http_response_code(503);
                $response['error'] = true;
                $response['message'] = 'Sorry! No Data Found';
            }
            //Check User Access End
        } else {
			http_response_code(400);
            $response['error'] = true;
            $response['message'] = "Access Denied";
        }
    } else {
		http_response_code(400);
        $response['error'] = true;
        $response['message'] = "Sorry! some details are missing";
    }
    //Check empty Parameters End
} catch (PDOException $e) {
    http_response_code(503);
    $response['error'] = true;
    $response['message'] = "Connection failed" . $e->getMessage();;
}
echo json_encode($response);
$pdoread = null;
?>
