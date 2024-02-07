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
$admissionno = trim($data->admissionno);
$doc_uid = trim($data->doc_uid);
$umrno = trim($data->umrno);
$sno = trim($data->sno);
$notes = trim($data->notes);
$doctor_name = ($data->doctor_name);
$shift_type = trim($data->shift_type);
$specialisations = trim($data->specialisations);
try {
    if (!empty($accesskey) && !empty($admissionno) && !empty($umrno)  && !empty($sno)) {

        //Check User Access Start
        $check = $pdoread->prepare("SELECT `userid`,`username`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check->execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if ($check->rowCount() > 0) {
             //check if patient discharged or not
             $validate = $pdoread -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
             $validate->bindParam(':ip', $admissionno, PDO::PARAM_STR);
             $validate -> execute();
             $validates = $validate->fetch(PDO::FETCH_ASSOC);
             if($validate -> rowCount() > 0){

            // $notess = str_replace(array("\r", "\n"), '', strip_tags($notes));
            $update = $pdo4->prepare("UPDATE `doctor_progress_notes` SET `notes`=:notes, `doctor_uid`=:doctor_uid, `doctor_name`=:doctor_name,`specialisations`=:specialisations,`modifiedby`=:userid, `modifiedon`=CURRENT_TIMESTAMP,`shift_type`=:shift_type WHERE `admissionno` = :admissionno AND `umrno`=:umrno AND `sno` =:sno AND `estatus`='Active' ");
            $update->bindParam(':notes', $notes, PDO::PARAM_STR);
            $update->bindParam(':admissionno', $admissionno, PDO::PARAM_STR);
            $update->bindParam(':umrno', $umrno, PDO::PARAM_STR);
            $update->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
            $update->bindParam(':sno', $sno, PDO::PARAM_STR);
            $update->bindParam(':doctor_uid', $doc_uid, PDO::PARAM_STR);
            $update->bindParam(':doctor_name', $doctor_name, PDO::PARAM_STR);
            $update->bindParam(':shift_type', $shift_type, PDO::PARAM_STR);
            $update->bindParam(':specialisations', $specialisations, PDO::PARAM_STR);
            $update->execute();
            if ($update->rowCount() > 0) {
				 http_response_code(200);
                $response['error'] = false;
                $response['message'] = "Updated Successfully";
            } else {
				http_response_code(503);
                $response['error'] = true;
                $response['message'] = "Not Updated";
            }

            //Check User Access End
        }else{
			 http_response_code(503);
            $response['error'] = true;
              $response['message']= "You have entered incorrect IP Number / Patient Checked Out";
        }
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
    $response['message'] = "Connection failed" . $e->getMessage();
}
echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>
