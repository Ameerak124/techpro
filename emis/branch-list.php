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
$accesskey = $data->accesskey;
$response = array();
$response1 = array();
try
{
if(!empty($accesskey)){

 $check = $pdoread -> prepare("SELECT `userid`,`username`,mobile_accesskey AS `accesskey`,`branch` AS storeaccess FROM `user_logins` WHERE `mobile_accesskey`=:accesskey AND `status`= 'Active' LIMIT 1");
$check -> bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$location = explode(",",$result['storeaccess']);
$i = 0;
if($check -> rowCount() > 0){
	
	
	foreach($location as $branche){
	if($branche==" " || $branche=="MCNMI" || $branche=="ERGD")
	{
		/* http_response_code(503);
	$response['error']= true;
	$response['message']="No data found"; */
	}else{
		$branch = $pdoread -> prepare("SELECT `display_name` AS dbranch FROM `branch_master` WHERE `cost_center` = :branche LIMIT 1");
	$branch->bindParam(':branche', $branche, PDO::PARAM_STR);
	$branch -> execute();
	$branchres = $branch->fetch(PDO::FETCH_ASSOC);

	http_response_code(200);
	$response['error']= false;
	$response['message']="Data found";
	
	$temp=[
	"display"=>$branchres['dbranch'],
	"value"=> $branche
	
	];
	array_push($response1,$temp);

	$i++;	
	}

		
	}
	
	$sorted = array_orderby($response1, 'display', SORT_ASC, 'value', SORT_ASC);
	$response['branchlist'] = $sorted;
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
	$response['message']= "Connection failed: " . $e;
}
	



echo json_encode($response);
   unset($pdoread);
   
   
   function array_orderby()
{
    $args = func_get_args();
    $data = array_shift($args);
    foreach ($args as $n => $field) {
        if (is_string($field)) {
            $tmp = array();
            foreach ($data as $key => $row)
                $tmp[$key] = $row[$field];
            $args[$n] = $tmp;
            }
    }
    $args[] = &$data;
    call_user_func_array('array_multisort', $args);
    return array_pop($args);
}
?>