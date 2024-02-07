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
$response = array();
$data = json_decode(file_get_contents("php://input"));
$accesskey = trim($data->accesskey);
$umrno = trim($data->umrno);
$ip = trim($data->ip);
try {
    if (!empty($accesskey) && !empty($ip) && !empty($umrno)) {
        //Check User Access Start
        $check = $pdoread->prepare("SELECT `userid`,`username`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check->execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if ($check->rowCount() > 0) {
            $list = $pdoread->prepare("SELECT @a:=@a+1 serial_number, 
   n.`sno`, `admissionno`, `umrno`, `doctor_uid`,`doctor_name`, `specialisations`, `ward`, `notes`,`shift_type`,Date_FORMAT (n.`createdon`, '%d-%M-%Y') as date,
   u.`username` as doc_nmae,
   DATE_FORMAT(n.`modifiedon`, '%d-%b-%Y %h:%i %p') AS ondate,
   `approved_status`,if(approved_status='Approved','#008000',if(approved_status='Rejected','#D22B2B','')) as colour,
   u2.`username` as Approved_by,
   DATE_FORMAT(n.`approvedon`, '%d-%b-%Y %H:%i') as Approved_on,  
   `estatus`
FROM (SELECT @a:= 0) a 
INNER JOIN `doctor_progress_notes` n ON n.`admissionno` = :ip AND n.`umrno` = :umrno
LEFT JOIN `user_logins` u ON u.`userid` = n.`modifiedby`
LEFT JOIN `user_logins` u2 ON u2.`userid` = n.`approvedby`
ORDER BY n.`modifiedon` DESC");
            $list->bindParam(':ip', $ip, PDO::PARAM_STR);
            $list->bindParam(':umrno', $umrno, PDO::PARAM_STR);
            $list->execute();
            if ($list->rowCount() > 0) {
				http_response_code(200);
                $response['error'] = false;
                $response['message'] = "Data found";
                while ($results = $list->fetch(PDO::FETCH_ASSOC)) {
                    $response['doctorprogressnoteslist'][] = $results;
                }
            } else {
				http_response_code(503);
                $response['error'] = true;
                $response['message'] = "No data found";
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
