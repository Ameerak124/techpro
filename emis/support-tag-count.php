<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include 'pdo-db.php';
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
$fdate = date('Y-m-d', strtotime($data->fdate)); 
$tdate = date('Y-m-d', strtotime($data->tdate)); 
$branch = $data->branch;
$response = array();
$response1 = array();
try {
if(!empty($accesskey)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` ,`cost_center`,`role` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check -> bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$check -> execute();
$location = explode(",",$result['storeaccess']);
if($check -> rowCount() > 0){
$result = $check->fetch(PDO::FETCH_ASSOC);
if($result['role']=='Center Head'){
if($branch=='All'){

$stmt1=$pdoread->prepare("select (SELECT count(*) FROM mysupporters_mapping  WHERE mysupporters_mapping.status in ('Pending','Approved') AND   date(mysupporters_mapping.created_on) BETWEEN :fdate and :tdate) as totalbills,(SELECT count(*) FROM mysupporters_mapping WHERE  ch_status='Pending' AND mysupporters_mapping.status='Pending'  AND  date(mysupporters_mapping.created_on) BETWEEN :fdate and :tdate) as pendingbills,(SELECT count(*) FROM mysupporters_mapping WHERE  ch_status='Approved' AND mysupporters_mapping.status='Pending'  AND  date(mysupporters_mapping.created_on) BETWEEN :fdate and :tdate) as approvedbills,(SELECT count(*) FROM mysupporters_mapping WHERE  ch_status='Rejected' AND mysupporters_mapping.status='Pending'  AND  date(mysupporters_mapping.created_on) BETWEEN :fdate and :tdate) as rejectedbills");
$stmt1 -> bindParam(":fdate", $fdate, PDO::PARAM_STR);
$stmt1 -> bindParam(":tdate", $tdate, PDO::PARAM_STR);
$stmt1 -> execute();

}else{

$stmt1=$pdoread->prepare("select (SELECT count(*) FROM mysupporters_mapping  WHERE mysupporters_mapping.status in ('Pending','Approved') AND   date(mysupporters_mapping.created_on) BETWEEN :fdate and :tdate and mysupporters_mapping.branch =:cost_center) as totalbills,(SELECT count(*) FROM mysupporters_mapping WHERE  ch_status='Pending' AND mysupporters_mapping.status='Pending'  AND  date(mysupporters_mapping.created_on) BETWEEN :fdate and :tdate and mysupporters_mapping.branch =:cost_center) as pendingbills,(SELECT count(*) FROM mysupporters_mapping WHERE  ch_status='Approved' AND mysupporters_mapping.status='Pending'  AND  date(mysupporters_mapping.created_on) BETWEEN :fdate and :tdate and mysupporters_mapping.branch =:cost_center) as approvedbills,(SELECT count(*) FROM mysupporters_mapping WHERE  ch_status='Rejected' AND mysupporters_mapping.status='Pending'  AND  date(mysupporters_mapping.created_on) BETWEEN :fdate and :tdate and mysupporters_mapping.branch =:cost_center) as rejectedbills");
$stmt1 -> bindParam(":cost_center", $branch, PDO::PARAM_STR);
$stmt1 -> bindParam(":fdate", $fdate, PDO::PARAM_STR);
$stmt1 -> bindParam(":tdate", $tdate, PDO::PARAM_STR);
$stmt1 -> execute();
}




if($stmt1 -> rowCount() > 0){
	$list = $stmt1 -> fetch(PDO::FETCH_ASSOC);
http_response_code(200);
$response['error'] = false;
$response['message']="Data found";	


$my_array = array("Total Bills","Pending Bills","Approved Bills");
	$my_array1 = array($list['totalbills'],$list['pendingbills'],$list['approvedbills']);
	$my_array2 = array("Total","Pending","Approved");
	for($x = 0; $x < sizeof($my_array); $x++){	
	$temp=[
	"name"=>$my_array[$x],
	"value"=>number_format($my_array1[$x]),
	"status"=>($my_array2[$x]),
	];
	array_push($response1,$temp);
	}

$response['billingdiscountreport']= $response1;
}else{
http_response_code(503);
$response['error']= true;
$response['message']="No Data Found!";
}

}else{
	
    http_response_code(400);
	$response['error']= true;
	$response['message']="Unauthorized Access!";
}
}else{
http_response_code(400);
$response['error']= true;
$response['message']="Access denied!";
}
}else{
http_response_code(400);
$response['error']= true;
$response['message']="Sorry! some details are missing";
}
}catch(PDOException $e) {
http_response_code(503);
$response['error'] = true;
$response['message']= "Connection failed: ".$e;
}
echo json_encode($response);
unset($pdoread);
?>