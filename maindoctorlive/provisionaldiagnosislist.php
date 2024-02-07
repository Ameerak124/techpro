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
$accesskey = trim($data->accesskey);
$ipno = trim($data->ipno);
$umr_no = trim($data->umr_no);

$response = array();

try{
    if(!empty($accesskey) && !empty($ipno)&& !empty($umr_no))
    {
      
        $check = $pdoread->prepare("SELECT `userid`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check->execute();
        
        if($check->rowCount() > 0)
        {
            $query = $pdoread->prepare("SELECT `search_diagnosis`, `description`, `createdon`, `createdby`, `modifiedon`, `modifiedby`, `estatus`, `ip`, `umr` FROM `provisional_diagnosis` WHERE `ip`=:ipno AND `umr`=:umr_no AND `estatus` = 'Active' ORDER BY `modifiedon` DESC");
            
            $query->bindParam(':ipno', $ipno, PDO::PARAM_STR);
            $query->bindParam(':umr_no', $umr_no, PDO::PARAM_STR);
            $query->execute();
            $result = $query->fetchAll(PDO::FETCH_ASSOC);

            if($query->rowCount() > 0)
            {
				http_response_code(200);
                $response['error'] = false;
                $response['message'] = "Data Fetched Successfully";
				$response['created_on'] = "Created On";
				$response['created_by'] = "Created By";
				$response['diagnosis'] = "Diagnosis";
				$response['description'] = "Description";
                $response['data'] = $result;
            }
            else
            {
				http_response_code(503);
                $response['error'] = true;
                $response['message'] = "No Data Found";
				$response['created_on'] = "Created On";
				$response['created_by'] = "Created By";
				$response['diagnosis'] = "Diagnosis";
				$response['description'] = "Description";
            }
        }
        else
        {
			http_response_code(400);
            $response['error']=true;
            $response['message']='Access denied';
        }
    }
    else
    {
		http_response_code(503);
        $response['error']=true;
        $response['message']='sorry some details are missing';
    }
}
catch(PDOException $e){
	http_response_code(503);
    $response['error']=true;
    $response['message']='Error: '. $e->getMessage();
}

echo json_encode($response);
$pdoread = null;

?>