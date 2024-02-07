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
$accesskey=$data->accesskey;
$response = array();
$response1 = array();
if(!empty($accesskey)){
$accesscheck ="SELECT `userid`,`branch`,`cost_center`,username,role,androidpermissions,androidsubmenu,androiddashboard FROM `super_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount() > 0){
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$userid = $row['userid'];
	$mmsss=explode("," ,$row['androidpermissions']);
 foreach($mmsss as $key) { 
$percheck ="SELECT `menu`,`submenu` As name,`dashboardtitle` As title,`packagename` As packagename,`ioscontrollers` as webcontroller,`classname`, `classtype`,CONCAT(:baseurl,'appicons/',`d-icons`) AS icon,storyboardname,`d_url`,`d_url1` FROM `android_permissions_new` WHERE `sno`=:key and `source`='superapp'  ";
	$stmt2 = $pdoread->prepare($percheck);
	$stmt2->bindParam(":key", $key, PDO::PARAM_STR);
	$stmt2->bindParam(":baseurl", $baseurl, PDO::PARAM_STR);
	$stmt2->execute();
	if($stmt2->rowCount() > 0){
		while($rows = $stmt2->fetch(PDO::FETCH_ASSOC)){
	/* if($rows['name']=='Doc Assist'){
		$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/doctorassist/';
		$url1='11';
	}else if($rows['name']=='E Assist'){
		$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/employee/';
		$url1='11';
	}else if($rows['name']=='Procurement'){
		
		$url='http://13.235.101.8/po/mobile-api/';
		$url1='11';
	}else if($rows['name']=='I Assist'){
		$url='https://65.2.7.174/api-new/super-app/';
		$url1='11';
	}else if($rows['name']=='Patient Referral'){
		$url='http://65.1.253.99/referral-dashboard/';
		$url1='11';
	}else if($rows['name']=='E-MIS'){
		$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/emis/';
		$url1='11';
	}else if($rows['name']=='MIS'){
		$url='http://65.1.253.99/mis/new-api-2/';
		$url1='11';
	}else if($rows['name']=='Doctor'){
		//$url='http://13.232.176.192/mobile-api/livedoctor/';
		$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/maindoctorlive/';
		$url1='11';
	}else if($rows['name']=='Security Dashboard'){
		
		$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/super-app/';
		$url1='11';
	}else if($rows['name']=='HRMS'){
		
		$url='http://3.7.119.125/hr/new-api/';
		$url1='11';
	}else if($rows['name']=='Operations'){
		
		$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com./mobile-api';
		$url1='https://www.medicoverhospitals.in/apis/';
	
	}else if($rows['name']=='Dashboard'){
		$url='';
		$url1='';
	}
     else if($rows['name']=='Dynamic Form'){
		
	$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/dynamic/';
	$url1='11';
	
}   else if($rows['name']=='Permissions'){
		
	$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/permissions/';
	$url1='11';
}   else if($rows['name']=='Get Data'){
		
	$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/getdata/';
	$url1='11';
}   else if($rows['name']=='Journal'){
		
	$url='https://ovidsp.ovid.com/autologin';
	$url1='11';
}else if($rows['name']=='PACS'){
		
	$url='http://10.74.0.50/PACSLogin';
	$url1='11';
}else if($rows['name']=='My Article'){
		
	$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/maindoctorlive/';
	$url1='11';
}else if($rows['name']=='Clinical News'){
		
	$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/maindoctorlive/';
	$url1='11';
}else if($rows['name']=='International Operations'){
		
	$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/internationalreferral/';
	$url1='11';
}else if($rows['name']=='Appointment Slot Summary'){
		
	$url='http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/maindoctorlive/';
	$url1='11';
}else if($rows['name']=='Tech Support'){
		
	$url='https://api.whatsapp.com/send?phone=917981927229&text=test';
	$url1='11';
} */
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
"url"=>$rows['d_url'],
"url1"=>$rows['d_url1']
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
echo json_encode($response);
$pdoread= null;
?>