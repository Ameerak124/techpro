<?php
 header("Content-Type: application/json; charset=UTF-8");
 include "pdo-db.php";
 $data=json_decode(file_get_contents("php://input"));
 $umrno=$data->umrno;
 $accesskey =trim($data->accesskey);
 $umrno =trim($data->umrno);
 $response=array();
 $ipaddress = $_SERVER['REMOTE_ADDR'];
 $apiurl = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); 
 $mybrowser = get_browser(null, true);
 try {
	 if(!empty($accesskey) && !empty($umrno)) {
$pdoread = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
// set the PDO error mode to exception
$pdoread->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//echo "Connected successfully";//
$check = $pdoread->prepare("SELECT `userid`,`cost_center`  FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->execute();
$result=$check->fetch(PDO::FETCH_ASSOC);
//fetched details from user logins//
if($check->rowcount() > 0) {
//get fetch
$fetch_data=$pdoread->prepare("SELECT `umr_no`, `admission_no`, `created_name`, `consent_form`, `insertion_type`, `adhered_to`, `site_of_insertion`, `reason_for_insertion`, `size`, `number_of_attempts`, `failure_reason`, `created_by` ,DATE_FORMAT(`created_on`,'%d-%b-%Y') AS created_date, DATE_FORMAT(`created_on`,'%H:%i %p') AS created_time,`lot_no`  FROM `nursing_pheripheral_venous` WHERE  `page_id`='V2' AND `umr_no`=:umrno AND `status`='Active' ORDER BY `modified_on` DESC;");
$fetch_data->bindParam(':umrno', $umrno, PDO::PARAM_STR);
$fetch_data->execute();
if($fetch_data->rowCount() > 0){
$sn=0;
	$response['error']=false;
	$response['message']='Data Found';
	while($res=$fetch_data->fetch(PDO::FETCH_ASSOC)){
	$response['data'][$sn]=$res;
     $sn++;
    }
}else{
	$response['error'] = true;
	$response['message']= "No Data Found";
}

}else{
	$response['error'] = true;
	$response['message']= "Access denied! Please try to re-login";
}
//Check User Access End
}else{
	$response['error'] = true;
	$response['message']= "Sorry! some details are missing";
}
//Check empty Parameters End
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed";
	$errorlog = $pdoread -> prepare("INSERT IGNORE INTO `error_logs`(`sno`, `created_by`, `created_on`, `message`, `api_url`) VALUES (NULL,:userid,CURRENT_TIMESTAMP,:errmessage,:apiurl)");
 $errorlog->bindParam(':userid', $result['userid'], PDO::PARAM_STR);
 $errorlog->bindParam(':errmessage', $e->getMessage(), PDO::PARAM_STR);
 $errorlog->bindParam(':apiurl', $apiurl, PDO::PARAM_STR);
 $errorlog -> execute();
 }
echo json_encode($response);
$pdoread = null;
?>