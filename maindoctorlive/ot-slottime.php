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
$otcode=$data->otcode;
$accesskey=$data->accesskey;
$selectdate=date_format(date_create($data->selectdate),"Y-m-d");
$response=array();
 try {
if( !empty($otcode) && !empty($accesskey)&& !empty($selectdate)) {
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);

$query1=$pdoread->prepare("SELECT * FROM `ot_master` WHERE `otcode` =:otcode AND `otstatus` ='Active'");
$query1->bindParam(':otcode', $otcode, PDO::PARAM_STR);
$query1 -> execute();
$result1 = $check->fetch(PDO::FETCH_ASSOC);

if ($query1->rowcount()>0)
	{ 
	$query2=$pdoread->prepare("SELECT E.mydate,DATE_FORMAT(E.mydate,'%h:%i %p') AS dispalytime,if(v.start_time != '','',DATE_FORMAT(E.mydate,'%H')) AS slottime,if(v.start_time != '','Booked','Vacant') AS slotstatus  FROM (SELECT DATE(:selectdate) + interval (seq * 60) Minute as mydate FROM seq_0_to_23) AS E LEFT JOIN (SELECT `otcode`,`start_time`,`end_time` FROM `ot_booking` WHERE `otcode` = :otcode AND DATE(`start_time`) = :selectdate) AS v ON E.mydate = v.start_time ORDER BY E.mydate ASC");
           $query2->bindParam(':otcode', $otcode, PDO::PARAM_STR);
		   $query2->bindParam(':selectdate', $selectdate, PDO::PARAM_STR);
           $query2 -> execute();
		   if($query2->rowcount()>0)
			{
				http_response_code(200);
			    $response['error']=false;
			   $response['message']='Data found';
			     while($result2 =$query2->fetch(PDO::FETCH_ASSOC)) {
                  $response['otslottimelist'][]=$result2;
			 
		   }
}else{
	     http_response_code(503);
         $response['error']=true;
		 $response['message']='No Data found';
}

		 }else{
			 http_response_code(400);
             $response['error']=true;
		 $response['message']='Access denied!';
}
}else {
	http_response_code(400);
	        $response['error']=true;
		 $response['message']='Sorry! some details are missing';;
             
         }
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>