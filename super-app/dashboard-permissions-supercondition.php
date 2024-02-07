<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
include "pdo-db-new.php";
$data = json_decode(file_get_contents("php://input"));
$accesskey=$data->accesskey;
$response = array();
$response1 = array();
try{
if(!empty($accesskey)){
$accesscheck ="SELECT `userid`,`branch`,`cost_center`,username,role,androidpermissions,androidsubmenu,androiddashboard FROM `super_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount() > 0){
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$userid = $row['userid'];
	$mmsss=explode("," ,$row['androiddashboard']);
 foreach($mmsss as $key) { 
$percheck ="SELECT `menu`,`submenu` As name,`dashboardtitle` As title,`packagename` As packagename,`ioscontrollers` as webcontroller,`classname`, `classtype`,CONCAT(:baseurl,'appicons/',`d-icons`) AS icon,storyboardname FROM `android_permissions_new` WHERE `sno`=:key and source='superapp'";
	$stmt2 = $pdoread->prepare($percheck);
	$stmt2->bindParam(":key", $key, PDO::PARAM_STR);
	$stmt2->bindParam(":baseurl", $baseurl, PDO::PARAM_STR);
	$stmt2->execute();
	$rows = $stmt2->fetch(PDO::FETCH_ASSOC);
	if($stmt2->rowCount() > 0){
                                              //Doctor Assist//
if( $rows['name']=='Doctor Assist'){
	$fetch = $pdoread -> prepare("SELECT `userid`,`username`,`mobile_accesskey`,`branch` AS storeaccess ,cost_center,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status`= 'Active' and role='Doctor'");
	$fetch->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	$fetch -> execute();
		if($fetch->rowCount()>0){
			
	$check ="SELECT `sno`, `status`, `source` FROM `android_permissions_new` WHERE `source`='Doctor Assist' and `status`='Active'";
	$stmt1 = $pdoread->prepare($check);
	$stmt1->execute();

$statuss='0';
	while($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)){

		
	
	$checkper=$pdoread->prepare("SELECT `userid` FROM `user_logins` WHERE `userid` = :empid and `status`='Active' and FIND_IN_SET(:sno,androiddashboard)<>0");
	$checkper->bindParam(":empid", $userid, PDO::PARAM_STR);
	$checkper->bindParam(":sno", $row1['sno'], PDO::PARAM_STR);
	$checkper->execute();
		
		if($checkper->rowCount()==1){
			
		$rest = $checkper->fetch(PDO::FETCH_ASSOC);
		$status ='1';
		}else{
		$status ='0';	
		}
		
	$statuss=$statuss + $status;	
 }
 if($statuss > '0'){; 
		$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/doctorassist/';
		$url1='11';
	}else{
	    $url='';
		$url1='';
	}
	}else{
	    $url='';
		$url1='';
	}
	                                        //Employee Assist//
}else if($rows['name']=='Employee Assist'){
	
$fetch = $pdoread -> prepare("SELECT `userid`,`username`,`mobile_accesskey`,`branch` AS storeaccess ,cost_center,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status`= 'Active'");
	$fetch->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	$fetch -> execute();
	if($fetch->rowCount()>0){
		
		$check ="SELECT `sno`, `status`, `source` FROM `android_permissions_new` WHERE `source`='E assist' and `status`='Active'";
	$stmt1 = $pdoread->prepare($check);
	$stmt1->execute();
$statuss='0';
	while($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)){

	$checkper=$pdoread->prepare("SELECT `userid` FROM `user_logins` WHERE `userid` = :empid and `status`='Active' and FIND_IN_SET(:sno,androiddashboard)<>0");
	$checkper->bindParam(":empid", $userid, PDO::PARAM_STR);
	$checkper->bindParam(":sno", $row1['sno'], PDO::PARAM_STR);
	$checkper->execute();
		
		if($checkper->rowCount()==1){
			
		$rest = $checkper->fetch(PDO::FETCH_ASSOC);
		$status ='1';
		}else{
		$status ='0';	
		}
		
	$statuss=$statuss + $status;	
	}
 if($statuss > '0'){; 
		$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/employee/';
		$url1='11';
	}else{
	    $url='';
		$url1='';
	}
 
	
	}else{
	    $url='';
		$url1='';
	}
	                                           //Procurement//
											   
 }else if($rows['name']=='Procurement'){
	$result3 = $pdo2 -> prepare("SELECT `empid` as userid, `designation`, `name` as username, `branch` AS storeaccess, `emailid`, `department`,password,role,accesskey FROM `pologins` where accesskey=:accesskey ");
	$result3->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	$result3 -> execute();	
	if($result3->rowCount()>0){	
	
	$check ="SELECT `sno`, `menu`, `submenu`, `titlename`, `packagename`, `substatus`, `permission_tablenames`, `icons`, `dashboardicons`, `status`, `iosviewcontrollers` FROM `android_permissions` WHERE `status`='Active'";
	$stmt1 = $pdo2->prepare($check);
	$stmt1->execute();
     $statuss='0';
	while($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)){
	
	$checkper=$pdo2->prepare("SELECT `empid` FROM `pologins` WHERE `empid`=:empid and status='Active'  and FIND_IN_SET(:sno,androiddashboard)<>0");
	$checkper->bindParam(":empid", $userid, PDO::PARAM_STR);
	$checkper->bindParam(":sno", $row1['sno'], PDO::PARAM_STR);
	$checkper->execute();
		
		if($checkper->rowCount()==1){
			
		$rest = $checkper->fetch(PDO::FETCH_ASSOC);
		$status ='1';
		}else{
		$status ='0';	
		}
		
	$statuss=$statuss + $status;	
 }
 if($statuss > '0'){; 
		$url='http://13.235.101.8/po/mobile-api/';
		$url1='11';
	}else{
	    $url='';
		$url1='';
	}
	}else{
	    $url='';
		$url1='';
	}
	                                               //I Assist//
}else if($rows['name']=='I Assist'){
	$result2 = $pdo1 -> prepare("SELECT  `emp_id` as userid, `emp_name` as username, `designation` as designation, `department` as department, `branch` AS storeaccess ,`clinicaltype`,role,mpassword,password,accesstoken FROM `emp_logins` where accesstoken=:accesskey");
	$result2->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	$result2-> execute();
	if($result2->rowCount()>0){
	$check ="SELECT `sno`, `menu`, `polish_menu`, `submenu`, `polish_submenu`, `titlename`, `polish_titlename`, `dashboardtitle`, `ioscontrollers`, `packagename`, `classname`, `classtype`, `substatus`, `d-icons`, `side-icons`, `status` FROM `android_permissions` WHERE `status`='Active'";
	$stmt1 = $pdo1->prepare($check);
	$stmt1->execute();
     $statuss='0';
	while($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)){
	
	$checkper=$pdo1->prepare("SELECT `emp_id` FROM `emp_logins` WHERE `emp_id`=:empid  and FIND_IN_SET(:sno,androidpermissions)<>0");
	$checkper->bindParam(":empid", $userid, PDO::PARAM_STR);
	$checkper->bindParam(":sno", $row1['sno'], PDO::PARAM_STR);
	$checkper->execute();
		
		if($checkper->rowCount()==1){
			
		$rest = $checkper->fetch(PDO::FETCH_ASSOC);
		$status ='1';
		}else{
		$status ='0';	
		}
		
	$statuss=$statuss + $status;	
 }
 if($statuss > '0'){; 
		$url='https://65.2.7.174/api-new/super-app/';
		$url1='11';
	}else{
	    $url='';
		$url1='';
	}
	}else{
	    $url='';
		$url1='';
	}
	   
	                                       //Patient Referral//
}else if($rows['name']=='Patient Referral'){
	$insert ="SELECT  `Emp_name`, `Designation`, `Emp_ID`, `Password`, `mpassword`, `Branch` AS storeaccess, `Job_Status`, `branch_access`,type,`branch_access`,accesskey  FROM `referral_logins` WHERE `Job_Status`='Active' and `accesskey`=:accesskey";
	$result4 = $con->prepare($insert);
    $result4->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$result4->execute();
	if($result4->rowCount()>0){
		$check ="SELECT `sno`, `menu`, `submenu`, `titlename`, `dashboardtitle`, `ioscontrollers`, `packagename`, `classname`, `classtype`, `substatus`, `d-icons`, `side-icons`, `status`, `mis`, `referral` FROM `android_permissions_new` WHERE `referral`='1' and status='Active'";
	$stmt1 = $con->prepare($check);
	$stmt1->execute();
   $statuss='0';
	while($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)){
		
	
	$checkper=$con->prepare("SELECT `Emp_ID` FROM `referral_logins` where `Emp_ID`=:empid and Job_Status='Active' and (FIND_IN_SET(:sno,androidpermissions)<>0 or FIND_IN_SET(:sno,androidsubmenu)<>0)");
	$checkper->bindParam(":empid", $userid, PDO::PARAM_STR);
	$checkper->bindParam(":sno", $row1['sno'], PDO::PARAM_STR);
	$checkper->execute();
		
		if($checkper->rowCount()==1){
			
		$rest = $checkper->fetch(PDO::FETCH_ASSOC);
		$status ='1';
		}else{
		$status ='0';	
		}
		
	$statuss=$statuss + $status;	
 }
 if($statuss > '0'){; 
		$url='http://65.1.253.99/referral-dashboard/';
		$url1='11';
	}else{
	    $url='';
		$url1='';
	}
	}else{
	    $url='';
		$url1='';
	}
	                                                   //E Mis//
}else if($rows['name']=='E-MIS'){
	$result5 = $pdoread -> prepare("SELECT `userid`, `password`, `username`, `emailid`, `role`, `desgination`, `department`, `status`, `cost_center`,`branch`, `accesskey`, `mobile_accesskey` FROM `user_logins` WHERE `status`='Active' and `mobile_accesskey`=:accesskey");
	$result5 ->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	$result5  -> execute();
	if($result5 -> rowCount() > 0){
		$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/emis/';
		$url1='11';
	}else{
		$url='';
		$url1='';
	}
	                                            //MIS//
}else if($rows['name']=='MIS'){
	$insert1 ="SELECT  `Emp_name`, `Designation`, `Emp_ID`, `Password`, `mpassword`, `Branch` AS storeaccess, `Job_Status`, `branch_access`,type,`branch_access`,accesskey,Office_number FROM `logins` WHERE `Job_Status`='Active' and `accesskey`=:accesskey";
    $results = $con->prepare($insert1);
    $results->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
    $results->execute();
	if($result4->rowCount()>0){
	$check ="SELECT `sno`, `menu`, `submenu`, `titlename`, `dashboardtitle`, `ioscontrollers`, `packagename`, `classname`, `classtype`, `substatus`, `d-icons`, `side-icons`, `status`, `mis`, `referral` FROM `android_permissions_new` WHERE `mis`='1' and status='Active'";
	$stmt1 = $con->prepare($check);
	$stmt1->execute();
   $statuss='0';
	while($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)){
		
	
	$checkper=$con->prepare("SELECT `Emp_ID`  FROM `logins` WHERE `Emp_ID` = :empid and Job_Status='Active' and (FIND_IN_SET(:sno,androidpermissions)<>0 or FIND_IN_SET(:sno,androidsubmenu)<>0)");
	$checkper->bindParam(":empid", $userid, PDO::PARAM_STR);
	$checkper->bindParam(":sno", $row1['sno'], PDO::PARAM_STR);
	$checkper->execute();
		
		if($checkper->rowCount()==1){
			
		$rest = $checkper->fetch(PDO::FETCH_ASSOC);
		$status ='1';
		}else{
		$status ='0';	
		}
		
	$statuss=$statuss + $status;	
 }
 if($statuss > '0'){; 
		$url='http://65.1.253.99/mis/new-api-2/';
		$url1='11';
	}else{
	    $url='';
		$url1='';
	}
	}else{
	    $url='';
		$url1='';
	}
                                      //Doctor//
}else if($rows['name']=='Doctor'){
	$result5 = $pdoread -> prepare("SELECT `userid`, `password`, `username`, `emailid`, `role`, `desgination`, `department`, `status`, `cost_center`,`branch`, `accesskey`, `mobile_accesskey` FROM `user_logins` WHERE `status`='Active' and `mobile_accesskey`=:accesskey and role like '%Doctor%'");
	$result5 ->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	$result5  -> execute();
	if($result5 -> rowCount() > 0){
		$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/maindoctorlive/';
		$url1='11';
	}else{
		$url='';
		$url1='';
	}
	                                             //Security Dashboard//
}else if($rows['name']=='Security Dashboard'){
	$fetch = $pdoread -> prepare("SELECT `userid`,`username`,`mobile_accesskey`,`branch` AS storeaccess ,cost_center,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status`= 'Active'");
	$fetch->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	$fetch -> execute();
	if($fetch->rowCount()>0){
		
			
		$check ="SELECT `sno`, `status`, `source` FROM `android_permissions_new` WHERE `source`='Security' and `status`='Active'";
	$stmt1 = $pdoread->prepare($check);
	$stmt1->execute();
	$statuss='0';
	while($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)){
	
	$checkper=$pdoread->prepare("SELECT `userid` FROM `user_logins` WHERE `userid` = :empid and `status`='Active' and FIND_IN_SET(:sno,androiddashboard)<>0");
	$checkper->bindParam(":empid", $userid, PDO::PARAM_STR);
	$checkper->bindParam(":sno", $row1['sno'], PDO::PARAM_STR);
	$checkper->execute();
		
		if($checkper->rowCount()==1){
			
		$rest = $checkper->fetch(PDO::FETCH_ASSOC);
		$status ='1';
		}else{
		$status ='0';	
		}
		
	$statuss=$statuss + $status;	
 }
 if($statuss > '0'){; 
		$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/super-app/';
		$url1='11';
	}else{
	    $url='';
		$url1='';
	}
	}else{
	    $url='';
		$url1='';
	}
	                                                    //HRMS//
}else if($rows['name']=='HRMS'){
	$result6 = $pdo_hrms -> prepare("SELECT `empid` as userid, `employee_name` as username, concat( `first_name`, `middle_name`, `last_name`) as name, `branch`, `designation`,accesskey,roles as role FROM `employee_details` WHERE `status`='Active' and `accesskey`=:accesskey");
	$result6 ->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	$result6  -> execute();
	if($result6 -> rowCount() > 0){	

	 $check ="SELECT sno,`menu`,`submenu` As name,`dashboardtitle` As title,`packagename` As packagename,`classname`, `classtype`,'' AS icon FROM `android_permissions`";
	$stmt1 = $pdo_hrms->prepare($check);
	$stmt1->execute();
	$statuss='0';
	while($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)){
    
	$checkper=$pdo_hrms->prepare("SELECT `empid` FROM `employee_details` WHERE `empid` = :empid and `status`='Active' and (FIND_IN_SET(:sno,androiddashboard)<>0 or FIND_IN_SET(:sno,androidpermissions)<>0 or FIND_IN_SET(:sno,androidsubmenu)<>0)");
	$checkper->bindParam(":empid", $userid, PDO::PARAM_STR);
	$checkper->bindParam(":sno", $row1['sno'], PDO::PARAM_STR);
	$checkper->execute();
    
    if($checkper->rowCount()==1){
			
		$rest = $checkper->fetch(PDO::FETCH_ASSOC);
		$status ='1';
		}else{
		$status ='0';	
		}
		
	$statuss=$statuss + $status;	
 }
 if($statuss > '0'){; 
		$url='http://3.7.119.125/hr/new-api/';
		$url1='11';
	}else{
	    $url='';
		$url1='';
	}
	}else{
	    $url='';
		$url1='';
	} 
                                                      	//Operations//
}else if($rows['name']=='Operations'){
	$result5 = $pdoread -> prepare("SELECT `userid`, `password`, `username`, `emailid`, `role`, `desgination`, `department`, `status`, `cost_center`,`branch`, `accesskey`, `mobile_accesskey` FROM `user_logins` WHERE `status`='Active' and `mobile_accesskey`=:accesskey");
	$result5 ->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	$result5  -> execute();
	if($result5 -> rowCount() > 0){
		
			$check ="SELECT `sno`, `status`, `source` FROM `android_permissions_new` WHERE `source`='Operations' and `status`='Active'";
	$stmt1 = $pdoread->prepare($check);
	$stmt1->execute();
    $statuss='0';
	while($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)){

	$checkper=$pdoread->prepare("SELECT `userid` FROM `user_logins` WHERE `userid` = :empid and `status`='Active' and FIND_IN_SET(:sno,androiddashboard)<>0");
	$checkper->bindParam(":empid", $userid, PDO::PARAM_STR);
	$checkper->bindParam(":sno", $row1['sno'], PDO::PARAM_STR);
	$checkper->execute();
		
		if($checkper->rowCount()==1){
			
		$rest = $checkper->fetch(PDO::FETCH_ASSOC);
		$status ='1';
		}else{
		$status ='0';	
		}
		
	$statuss=$statuss + $status;	
 }
 if($statuss > '0'){; 
		$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/';
		$url1='https://www.medicoverhospitals.in/apis/';
	}else{
	    $url='';
		$url1='';
	}
	}else{
	    $url='';
		$url1='';
	}
	                                                   //Dynamic Form//
}else if($rows['name']=='Dynamic Form'){
	$fetch = $pdoread -> prepare("SELECT `userid`,`username`,`mobile_accesskey`,`branch` AS storeaccess ,cost_center,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status`= 'Active'");
	$fetch->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	$fetch -> execute();
	if($fetch->rowCount()>0){	
	$check ="SELECT `sno`, `status`, `source` FROM `android_permissions_new` WHERE `source`='dynamic' and `status`='Active'";
	$stmt1 = $pdoread->prepare($check);
	$stmt1->execute();
    $statuss='0';
	while($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)){

	$checkper=$pdoread->prepare("SELECT `userid` FROM `user_logins` WHERE `userid` = :empid and `status`='Active' and FIND_IN_SET(:sno,androiddashboard)<>0");
	$checkper->bindParam(":empid", $userid, PDO::PARAM_STR);
	$checkper->bindParam(":sno", $row1['sno'], PDO::PARAM_STR);
	$checkper->execute();
		
		if($checkper->rowCount()==1){
			
		$rest = $checkper->fetch(PDO::FETCH_ASSOC);
		$status ='1';
		}else{
		$status ='0';	
		}
		
	$statuss=$statuss + $status;	
 }
 if($statuss > '0'){; 
		$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/dynamic/';
		$url1='11';
	}else{
	    $url='';
		$url1='';
	}
	}else{
	    $url='';
		$url1='';
	}
	                                                 // Permissions//
}   else if($rows['name']=='Permissions'){
	$fetch = $pdoread -> prepare("SELECT `userid`,`username`,`mobile_accesskey`,`branch` AS storeaccess ,cost_center,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status`= 'Active'");
	$fetch->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	$fetch -> execute();
	if($fetch->rowCount()>0){	
	$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/permissions/';
	$url1='11';
	}else{
		$url='';
		$url1='';
	}
                                                         //Get Data//	
}   else if($rows['name']=='Get Data'){
	$fetch = $pdoread -> prepare("SELECT `userid`,`username`,`mobile_accesskey`,`branch` AS storeaccess ,cost_center,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status`= 'Active'");
	$fetch->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
	$fetch -> execute();
	if($fetch->rowCount()>0){	
	$check ="SELECT `sno`, `status`, `source` FROM `android_permissions_new` WHERE `source`='getdata' and `status`='Active'";
	$stmt1 = $pdoread->prepare($check);
	$stmt1->execute();
     $statuss='0';
	while($row1 = $stmt1->fetch(PDO::FETCH_ASSOC)){		
	
	$checkper=$pdoread->prepare("SELECT `userid` FROM `user_logins` WHERE `userid` = :empid and `status`='Active' and FIND_IN_SET(:sno,androiddashboard)<>0");
	$checkper->bindParam(":empid", $userid, PDO::PARAM_STR);
	$checkper->bindParam(":sno", $row1['sno'], PDO::PARAM_STR);
	$checkper->execute();
		
		if($checkper->rowCount()==1){
			
		$rest = $checkper->fetch(PDO::FETCH_ASSOC);
		$status ='1';
		}else{
		$status ='0';	
		}
		
	$statuss=$statuss + $status;	
 }
 if($statuss > '0'){; 
		$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/getdata/';
		$url1='11';
	}else{
	    $url='';
		$url1='';
	}
	}else{
	    $url='';
		$url1='';
	}
}

if(!empty($url)){
$temp=[
"name"=>$rows['name'],
"displayname"=>$rows['title'],
"title"=>$rows['title'],
"packagename"=>$rows['packagename'],
"ioscontrollers"=>$rows['webcontroller'],
"storyboardname"=>$rows['storyboardname'],
"classname"=>$rows['classname'],
"classtype"=>$rows['classtype'],
"icon"=>$rows['icon'],
"url"=>$url,
"url1"=>$url1
];
array_push($response1,$temp);
	}
	
}else{
http_response_code(503);
$response['error'] = true;
$response['message']="No Data Found";
 }

 
http_response_code(200);
$response['error'] = false;
$response['message']="Data found";
$response['gridcount']="3";
$response['dashboardlist']=$response1;

 }
}else{
http_response_code(400);
$response['error']= true;
$response['message']="Access denied!";
}
}else{
	
http_response_code(400);
$response['error']= true;
$response['message']="Sorry some details missing";
}
}catch(PDOEXCEPTION $e){

	http_response_code(503);
	$response['error'] = true;
	$response['message'] = $e->getMessage();
}
echo json_encode($response);
$pdoread= null;
?>