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
$search_diagnosis = trim($data->search_diagnosis);
$description = trim($data->description);
$ipno = trim($data->ipno);
$umr = trim($data->umr);

try {
    if (!empty($accesskey) && !empty($ipno) && !empty($umr)) {
       
        $check = $pdoread->prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check->execute();

        if ($check->rowCount() > 0) {
            $result = $check->fetch(PDO::FETCH_ASSOC);
 $validate = $pdoread -> prepare("SELECT `admissionno`,`umrno` FROM `registration` WHERE `admissionno` = :ip AND `status` = 'Visible' AND `admissionstatus` NOT IN('Discharged')");
            $validate->bindParam(':ip', $ipno, PDO::PARAM_STR);
            $validate -> execute();
            $validates = $validate->fetch(PDO::FETCH_ASSOC);
            if($validate -> rowCount() > 0){
          
                $query = $pdo4->prepare("INSERT INTO provisional_diagnosis (`search_diagnosis`, `description`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `estatus`, `ip`, `umr`) VALUES (:search_diagnosis, :descr, CURRENT_TIMESTAMP, :userid, CURRENT_TIMESTAMP, :userid, 'Active', :ipno, :umr)");

                $query->bindParam(':search_diagnosis', $search_diagnosis, PDO::PARAM_STR);
                $query->bindParam(':descr', $description, PDO::PARAM_STR);
                $query->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
                $query->bindParam(':ipno', $ipno, PDO::PARAM_STR);
                $query->bindParam(':umr', $umr, PDO::PARAM_STR);
                $query->execute();

                if ($query->rowCount() > 0) {
					http_response_code(200);
                    $response['error'] = false;
                    $response['message'] = 'Data inserted successfully';
                } else {
					http_response_code(503);
                    $response['error'] = true;
                    $response['message'] = 'Data already inserted';
                }
               }else{
				   	http_response_code(503);
              $response['error'] = true;
              $response['message']= "You have entered incorrect IP Number / Patient Checked Out";
        }
        } else {
			http_response_code(503);
            $response['error'] = true;
            $response['message'] = 'Access denied';
        }
    } else {
		http_response_code(503);
        $response['error'] = true;
        $response['message'] = 'Sorry, some details are missing';
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}

echo json_encode($response);
$pdo4 = null;
$pdoread = null;
?>
