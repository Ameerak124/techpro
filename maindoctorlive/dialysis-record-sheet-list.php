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
$ipno = trim($data->ipno);
$umrno = trim($data->umrno);
$did = trim($data->did);

try {
    if (!empty($accesskey) && !empty($ipno) && !empty($umrno) && !empty($did)) {
       
        $check = $pdoread->prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
        $check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
        $check->execute();
        $result = $check->fetch(PDO::FETCH_ASSOC);
        if ($check->rowCount() > 0) {

            //select into the table
            $dr = $pdoread->prepare("SELECT `sno`, `did`, `ipno`, `umrno`, if(dates!='0000-00-00 00:00:00',DATE_FORMAT(`dates`,'%d-%b-%Y %h:%i%:%s'),'') as dates, `timeszone`, `bp`, `vp`, `uf`, `qb`, `heparin`, `remarks` FROM `dialysis_record_sheet` WHERE `ipno`=:ipno AND `umrno`=:umrno AND `did`=:did AND `cost_center`=:cost_center AND `estatus`='Active' order by sno DESC ");
            $dr->bindParam(':ipno', $ipno, PDO::PARAM_STR);
            $dr->bindParam(':umrno', $umrno, PDO::PARAM_STR);
            $dr->bindParam(':did', $did, PDO::PARAM_STR);
            $dr->bindParam(':cost_center', $result['cost_center'], PDO::PARAM_STR);
            $dr->execute();
            if ($dr->rowCount() > 0) {
				http_response_code(200);
                $response['error'] = false;
                $response['message'] = "Data Found";
           
            while ($results = $dr->fetch(PDO::FETCH_ASSOC)) {
              $response['dialysissheetlist'][]= $results;
              
               }
            } else {
					http_response_code(503);
                $response['error'] = true;
                $response['message'] = "Data Not Found";
            }
        } else {
				http_response_code(400);
            $response['error'] = true;
            $response['message'] = "Access denied!";
        }
    } else {
			http_response_code(400);
        $response['error'] = true;
        $response['message'] = "Sorry! Some details are missing";
    }
} catch (PDOException $e) {
    http_response_code(503);
    $response['error'] = true;
    $response['message'] = "Connection failed";
}

echo json_encode($response);
$pdoread = null;
?>
