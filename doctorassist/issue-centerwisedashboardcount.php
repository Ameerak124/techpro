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
$fdate = date('Y-m-d', strtotime($data->fdate)); 
$tdate = date('Y-m-d', strtotime($data->tdate)); 

$response = array();
$response1 = array();
try
{
if(!empty($accesskey)){
	

 $check = $pdoread -> prepare("SELECT `userid`,`branch`,`cost_center`,role,username,concat(TRIM(username),' - ','(',userid,')') as assigningperson FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check -> bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$location = explode(",",$result['storeaccess']);
$i = 0;
if($check -> rowCount() > 0){
	
	
	$stmt=$pdoread->prepare("SELECT `display_name`, `branch_name`, `cost_center` FROM `branch_master`  inner join doctor_raise_ticket on doctor_raise_ticket.branch= branch_master.cost_center  where display_name!='test' and date(doctor_raise_ticket.created_on) between :fdate and :tdate group by `cost_center`");
	$stmt->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$stmt->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	$stmt -> execute();
	if($stmt->rowCount()>0){
		
	$unittotal='0';	
	$corporatetotal='0';	
	$directortotal='0';	
	$edtotal='0';	
	$closedgedtotal='0';	
	while($result1 = $stmt->fetch(PDO::FETCH_ASSOC)){
		
		
		$revenue_list = $pdoread -> prepare("SELECT COALESCE((SELECT count(*) FROM `doctor_raise_ticket` WHERE `realstatus`in ('Pending at Center Head','Pending by Sub Center head') and `branch`=:branch and date(`created_on`) BETWEEN :fdate and :tdate),0) AS unitheadpending,COALESCE((SELECT count(*) FROM `doctor_raise_ticket` WHERE `realstatus`in ('Pending at Medical Head') and `branch`=:branch and date(`created_on`) BETWEEN :fdate and :tdate),0) AS medicalheadpending,COALESCE((SELECT count(*) FROM `doctor_raise_ticket` WHERE `realstatus`IN ('Pending by Corporate Head','Pending by Sub Corporate Head') and `branch`=:branch and date(`created_on`) BETWEEN :fdate and :tdate),0) AS pencorhead,COALESCE((SELECT count(*) FROM `doctor_raise_ticket` WHERE `realstatus`in ('Pending by Director Operations','Pending by Sub Director Operations') and `branch`=:branch and date(`created_on`) BETWEEN :fdate and :tdate),0) AS pendirector,COALESCE((SELECT count(*) FROM `doctor_raise_ticket` WHERE `realstatus`IN ('Pending by ED','Pending by Sub ED') and `branch`=:branch and date(`created_on`) BETWEEN :fdate and :tdate),0) AS pendinged,COALESCE((SELECT count(*) FROM `doctor_raise_ticket` WHERE (`realstatus` LIKE '%Resolve%' or `realstatus` LIKE  '%Reject%') and `branch`=:branch and date(`created_on`) BETWEEN :fdate and :tdate),0) AS closedged");
		$revenue_list->bindParam(':fdate', $fdate, PDO::PARAM_STR);
	$revenue_list->bindParam(':tdate', $tdate, PDO::PARAM_STR);
	$revenue_list->bindParam(':branch', $result1['cost_center'], PDO::PARAM_STR);
   	$revenue_list -> execute();
		
		
		
	
	$revenue_data = $revenue_list->fetch(PDO::FETCH_ASSOC);
	$my_array = array("Unit","Unit Head","Corporate Head","RD","ED","Closed");
	
	http_response_code(200);
	$response['error']= false;
	$response['message']="Data found";
   $response['title']="Trend Analysis";
		if($result['role']=='Medical Head'){
	$unitheadcount=$revenue_data['medicalheadpending'];	
		}else if($result['role']=='Center Head'){
			$unitheadcount=$revenue_data['unitheadpending'];
			
	}else{
	$unitheadcount=$revenue_data['unitheadpending'] + $revenue_data['medicalheadpending'];
	}
	
	$temp=[
	"unit"=> $result1['branch_name'],
	"unitcode"=> $result1['cost_center'],
	"unitheadcount"=>strval($unitheadcount),
	"unitheading"=>"Pending at Center Head",
	"corporateheadcount"=>$revenue_data['pencorhead'],
	"corporateheading"=>"Pending by Corporate Head",
	"diropercount"=>$revenue_data['pendirector'],
	"diroprheading"=>"Pending by Director Operations", 
	"edcount"=>$revenue_data['pendinged'],
	"edheading"=>"Pending by ED",
	"closedcount"=>$revenue_data['closedged'],
	"closedheading"=>"Closed",
	];
	$unittotal=$unittotal+$revenue_data['unitheadpending'];
$corporatetotal=$corporatetotal+$revenue_data['pencorhead'];
$directortotal=$directortotal+$revenue_data['pendirector'];
$edtotal=$edtotal+$revenue_data['pendinged'];
$closedgedtotal=$closedgedtotal+$revenue_data['closedged'];
	array_push($response1,$temp);
	}
	 $response['unittotal']=strval($unittotal);
 $response['corporatetotal']=strval($corporatetotal);
 $response['directortotal']=strval($directortotal);
 $response['closedgedtotal']=strval($closedgedtotal);
 $response['edtotal']=strval($edtotal);
 for($x = 0; $x < sizeof($my_array); $x++){
		$response['titlelist'][$x]['type']=$my_array[$x];
     }
     $response['issuedashboardlist'] = $response1;
	}else{
	http_response_code(503);
	$response['error']= true;
	$response['message']="No Data Found";
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
	$response['message']= "Connection failed: " . $e;
}
	



echo json_encode($response);
   //unset($pdo4);
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