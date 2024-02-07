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
$accesskey=trim($data->accesskey);	
$fdate = date('Y-m-d', strtotime($data->fdate)); 
$tdate = date('Y-m-d', strtotime($data->tdate)); 
$response = array();

try{

if(!empty($accesskey)){
$validate = $pdo -> prepare("SELECT `userid`,`branch`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$validate->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$validate -> execute();
$result1= $validate->fetch(PDO::FETCH_ASSOC);
if($validate -> rowCount() > 0){
	
	
$count = $pdo -> prepare("SELECT COALESCE((SELECT COUNT(*) FROM `employee_raise_ticket` WHERE DATE(employee_raise_ticket.created_on) between :fdate and :tdate and user_id=:empid),0) as Raised,(COALESCE((SELECT COUNT(*) FROM `employee_raise_ticket` WHERE status NOT LIKE '%Resolve%' and status NOT LIKE '%Reject%' AND DATE(employee_raise_ticket.created_on) between :fdate and :tdate and user_id=:empid ),0)) as opened,(COALESCE((SELECT COUNT(*) FROM `employee_raise_ticket` WHERE (status  LIKE '%Resolve%' or status LIKE '%Reject%') AND DATE(employee_raise_ticket.created_on) between :fdate and :tdate and user_id=:empid),0)) as Closed");
$count->bindParam(':fdate', $fdate, PDO::PARAM_STR);
$count->bindParam(':tdate', $tdate, PDO::PARAM_STR);
$count->bindParam(':empid', $result1['userid'], PDO::PARAM_STR);
$count -> execute();
if($count -> rowCount() > 0){
$result= $count->fetch(PDO::FETCH_ASSOC);
 
$my_array = array("Ticket","Open","Close");

$my_array1 = array($result['Raised'],$result['opened'],$result['Closed']);
	
		http_response_code(200);
		$response['error'] = false;
		$response['message']="Data found";
		$response['title']="Ticket List";
		    for($x = 0; $x < sizeof($my_array); $x++){
		$response['list'][$x]['title']=$my_array[$x];
		$response['list'][$x]['count']=$my_array1[$x];
     }	
	}else{
		http_response_code(400);
			$response['error'] = true;
			$response['message']="No Data Found";
	}	

	
    	}else{
			http_response_code(400);
			$response['error'] = true;
			$response['message']="Access denied!";
			}  
}else{
	http_response_code(400);
	$response['error'] = true;
	$response['message'] ="Sorry! Some details are missing";
	}
} 
catch(PDOException $e)
{
    die("ERROR: Could not connect. " . $e->getMessage());
}
echo json_encode($response);
$pdo = null;
	?>