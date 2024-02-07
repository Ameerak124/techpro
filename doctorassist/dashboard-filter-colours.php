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
$response = array();

try{

if(!empty($accesskey)){
$validate = $pdoread -> prepare("SELECT `userid`,`branch`,`cost_center` FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$validate->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$validate -> execute();
if($validate->rowCount()>0){
		$row = $validate->fetch();
	
	$validate1 = $pdoread -> prepare("SELECT if(`assign`='0','','assign') as assign,if(`escalate`='0','','escalate') as escalate,if(`resolve`='0','','resolve') as resolve,if(`reject`='0','','reject') as reject,if(`inprocessing`='0','','inprocessing') as inprocessing FROM `issue_level_designations` where `CL`='Doctor'");
//$validate->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$validate1 -> execute();
if($validate1->rowCount()>0){
		$row1 = $validate1->fetch();	
		if(($row1['assign']=='') && ($row1['escalate']!='') &&  ($row1['resolve']!='') && ($row1['reject']!='') && ($row1['inprocessing']!='')){
			$my_array = array("Pending","Escalated","Resolved","Rejected","Inprocessing");
            $my_array1 = array("#FF4B06CC","#FF91CC06","#F1732E","#FFA433D5","#FF336D");
			
		}else if(($row1['assign']=='') && ($row1['escalate']=='') &&  ($row1['resolve']!='') && ($row1['reject']!='') && ($row1['inprocessing']!='')){
			$my_array = array("Pending","Resolved","Rejected","Inprocessing");

       $my_array1 = array("#FF4B06CC","#F1732E","#FFA433D5","#FF336D");
			
		}else if(($row1['assign']!='') && ($row1['escalate']=='') &&  ($row1['resolve']!='') && ($row1['reject']!='') && ($row1['inprocessing']!='')){
			$my_array = array("Pending","Assigned","Resolved","Rejected","Inprocessing");

           $my_array1 = array("#FF4B06CC","#FFC74D0B","#F1732E","#FFA433D5","#FF336D");
			
		}else{
			
			$my_array = array("Pending","Assigned","Escalated","Resolved","Rejected","Inprocessing");

       $my_array1 = array("#FF4B06CC","#FFC74D0B","#FF91CC06","#F1732E","#FFA433D5","#FF336D");
			
		}

		http_response_code(200);
		$response['error'] = false;
		$response['message']="Data found";
		 for($x = 0; $x < sizeof($my_array); $x++){
		$response['list'][$x]['title']=$my_array[$x];
		$response['list'][$x]['colour']=$my_array1[$x];
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
//$pdo4 = null;
$pdoread = null;
	?>