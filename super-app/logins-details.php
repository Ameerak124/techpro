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
$response = array();
$accesskey = trim($data->accesskey);
try {
if(!empty($accesskey)){
$accesscheck ="SELECT `userid`,`branch`,`cost_center`,username,role,androidpermissions,androidsubmenu,androiddashboard,if(emp_image='',concat(:baseurl,'documents/profiles/medicover_logo.png'),concat(:baseurl,emp_image)) as image FROM `super_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->bindParam(":baseurl", $baseurl, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount() > 0){
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$userid = $row['userid'];

	$fetch = $pdoread -> prepare("SELECT `userid`,`username`,`mobile_accesskey`,`branch` AS storeaccess ,cost_center,role FROM `user_logins` WHERE `userid` = :userid AND `status`= 'Active'");
	$fetch->bindParam(':userid', $userid, PDO::PARAM_STR);
	//$fetch->bindParam(':password', $password, PDO::PARAM_STR);
	$fetch -> execute();
	if($stmt->rowCount()>0){
	$fetchres = $fetch->fetch(PDO::FETCH_ASSOC);
	
	$doctorname =$fetchres['username'];
	$doctoruserid =$fetchres['userid'];
	$doctorbranch =$fetchres['username'];
	$doctorcostcentercode =$fetchres['storeaccess'];
	$doctoraccesskey =$fetchres['mobile_accesskey'];
	$doctorrole =$fetchres['role'];
	$costcenter =$fetchres['cost_center'];
	}else{	
	$doctorname ='11111';
	$doctoruserid ='11111';
	$doctorbranch ='11111';
	$doctorcostcentercode ='11111';
	$doctoraccesskey ='11111';
	$doctorrole ='11111';
	$costcenter ='11111';
	
	}
	$result2 = $pdo1 -> prepare("SELECT  `emp_id` as userid, `emp_name` as username, `designation` as designation, `department` as department, `branch` AS storeaccess ,`clinicaltype`,role,mpassword,password,accesstoken FROM `emp_logins` where emp_id=:userid AND `Job_Status`='Active'");
	$result2->bindParam(':userid', $userid, PDO::PARAM_STR);
	$result2-> execute();
	if($result2->rowCount()>0){
	$fetchres1 = $result2->fetch(PDO::FETCH_ASSOC);
	/* $employeename =	$fetchres1['username'];
	$employeeuserid =	$fetchres1['userid'];
	$employeebranch =	$fetchres1['username'];
	$employeecostcentercode =	$fetchres1['storeaccess'];
	$employeeaccesskey = $fetchres1['accesstoken'];
	$employeerole = $fetchres1['role'];
	}else{	
	$employeename = '11111';
	$employeeuserid =	'11111';
	$employeebranch =	'11111';
	$employeecostcentercode =	'11111';
	$employeeaccesskey =	'11111';
	$employeerole =	'11111';
	} */
	$iassistname =	$fetchres1['username'];
	$iassistdesignation =$fetchres1['designation'];
	$iassistuserid =$fetchres1['userid'];
	$iassistbranch =$fetchres1['storeaccess'];
	$iassistaccesskey = $fetchres1['accesstoken'];
	$iassistrole = $fetchres1['role'];
	$iassisttype = $fetchres1['role'];
	$iassistdepartment = $fetchres1['department'];
	$costcenter = $fetchres1['storeaccess']; 
	}else{	
	$iassistname = '11111';
	$iassistdesignation ='11111';
	$iassistuserid ='11111';
	$iassistbranch ='11111';
	$iassistaccesskey ='11111';
	$iassistrole ='11111';
	$iassisttype ='11111';
	$iassistdepartment ='11111';
	$costcenter ='11111';
	}
	$result3 = $pdo2 -> prepare("SELECT `empid` as userid, `designation`, `name` as username, `branch` AS storeaccess, `emailid`, `department`,password,role,accesskey FROM `pologins` where empid=:userid and status='Active'");
	$result3->bindParam(':userid', $userid, PDO::PARAM_STR);
	$result3 -> execute();
	if($result3->rowCount()>0){
	$fetchres2 = $result3->fetch(PDO::FETCH_ASSOC);
	$poname =	$fetchres2['username'];
	$pouserid =$fetchres2['userid'];
	$pobranch =$fetchres2['storeaccess'];
	$poaccesskey =$fetchres2['accesskey'];
	$porole =$fetchres2['role'];
	$costcenter = $fetchres2['storeaccess'];
	}else{	
	$poname ='11111';
	$pouserid ='11111';
	$pobranch ='11111';
	$poaccesskey ='11111';
	$porole ='11111';
	$costcenter ='11111';
	}
	$insert ="SELECT  `Emp_name`, `Designation`, `Emp_ID`, `Password`, `mpassword`, `Branch` AS storeaccess, `Job_Status`, `branch_access`,type,`branch_access`,accesskey  FROM `referral_logins` WHERE `Job_Status`='Active' and `Emp_ID`=:empid";
	$result4 = $con->prepare($insert);
     $result4->bindParam(":empid", $userid, PDO::PARAM_STR);
	$result4->execute();
     if($result4->rowCount()>0){
	$fetchres3 = $result4->fetch(PDO::FETCH_ASSOC);

	$reffmisname =$fetchres3['Emp_name'];
	$reffmisdesignation =$fetchres3['Designation'];
	$reffmisuserid =$fetchres3['Emp_ID'];
	$reffmisbranch =$fetchres3['storeaccess'];
	$reffmisaccesskey =$fetchres3['accesskey'];
	$reffmisrole =$fetchres3['type'];
	$reffmistype =$fetchres3['type'];
	
	}else{	
	$reffmisname = '11111';
	$reffmisdesignation ='11111';
	$reffmisuserid ='11111';
	$reffmisbranch = '11111';
	$reffmisaccesskey =	'11111';
	$reffmisrole =	'11111';
	$reffmistype =	'11111';
	}
			
     $insert1 ="SELECT  `Emp_name`, `Designation`, `Emp_ID`, `Password`, `mpassword`, `Branch` AS storeaccess, `Job_Status`, `branch_access`,type,`branch_access`,accesskey,Office_number FROM `logins` WHERE `Job_Status`='Active' and `Emp_ID`=:empid";
			$results = $con->prepare($insert1);
               $results->bindParam(":empid", $userid, PDO::PARAM_STR);
			$results->execute();
			
		  if($results->rowCount()>0){	
			$fetchress = $results->fetch(PDO::FETCH_ASSOC);									
	$misname =$fetchress['Emp_name'];
	$misdesignation =$fetchress['Designation'];
	$misuserid =$fetchress['Emp_ID'];
	$misbranch =$fetchress['storeaccess'];
	$misaccesskey =$fetchress['accesskey'];
	$misrole =$fetchress['type'];
	$mistype =$fetchress['type'];
	$mismobile =$fetchress['Office_number'];
	}else{	
	$misname ='11111';
	$misdesignation ='11111';
	$misuserid ='11111';
	$misbranch ='11111';
	$misaccesskey ='11111';
	$misrole ='11111';
	$mistype ='11111';
	$mismobile ='11111';
	}	
				
	$result5 = $pdoread -> prepare("SELECT `userid`, `password`, `username`, `emailid`, `role`, `desgination`, `department`, `status`, `cost_center`,`branch`, `accesskey`, `mobile_accesskey` FROM `user_logins` WHERE `status`='Active' and `userid`=:empid");
	$result5 ->bindParam(':empid', $userid, PDO::PARAM_STR);
	$result5  -> execute();
	if($result5 -> rowCount() > 0){
	 $fetchres4 = $result5->fetch(PDO::FETCH_ASSOC);
   /*  if($updateid -> rowCount() > 0){ */
   
     $emisname =	$fetchres4['username'];
	$emidsesignation =	$fetchres4['desgination'];
	$emisuserid =	$fetchres4['userid'];
	$emisbranch = $fetchres4['branch'];
	$emisaccesskey = $fetchres4['mobile_accesskey'];
	$emisrole = $fetchres4['role'];
	$costcenter = $fetchres4['cost_center'];
	//$mismobile = $fetchress['Office_number'];
	}else{	
	$emisname ='11111';
	$emidsesignation ='11111';
	$emisuserid ='11111';
	$emisbranch ='11111';
	$emisaccesskey ='11111';
	$emisrole ='11111';
	$costcenter ='11111';
	}	
	$result6 = $pdo_hrms -> prepare("SELECT `empid` as userid, `employee_name` as username, concat( `first_name`, `middle_name`, `last_name`) as name, `branch`, `designation`,accesskey,roles as role,`reporting_to`, `reporting_officer`,department FROM `employee_details` WHERE `status`='Active' and `empid`=:empid");
	$result6 ->bindParam(':empid', $userid, PDO::PARAM_STR);
	$result6  -> execute();
	if($result6 -> rowCount() > 0){
	 $fetchres5 = $result6->fetch(PDO::FETCH_ASSOC);
   /*  if($updateid -> rowCount() > 0){ */
   
   $hrmsname =	$fetchres5['username'];
	$hrmsdesignation =	$fetchres5['desgination'];
	$hrmsuserid =	$fetchres5['userid'];
	$hrmsbranch = $fetchres5['branch'];
	$hrmsaccesskey = $fetchres5['accesskey'];
	$hrmsrole = $fetchres5['role'];
	$hrmsreportingto = $fetchres5['reporting_to'];
	$hrmsreportingofficer = $fetchres5['reporting_officer'];
	$hrmsdepartment = $fetchres5['department'];
	//$mismobile = $fetchress['Office_number'];
	}else{	
	$hrmsname = '11111';
	$hrmsdesignation ='11111';
	$hrmsuserid ='11111';
	$hrmsbranch = '11111';
	$hrmsaccesskey ='11111';
	$hrmsrole ='11111';
	$hrmsreportingto = '11111';
	$hrmsreportingofficer = '11111';
	$hrmsdepartment = '11111';
	}
	
/* 	$operaion = $pdo -> prepare("SELECT `userid`,`username`,`mobile_accesskey`,`branch` AS storeaccess ,cost_center,role FROM `user_logins` WHERE `userid` = :userid AND `status`= 'Active'");
	$operaion->bindParam(':userid', $userid, PDO::PARAM_STR);
	//$fetch->bindParam(':password', $password, PDO::PARAM_STR);
	$operaion -> execute();
	if($operaion->rowCount()>0){
	$operaions = $operaion->fetch(PDO::FETCH_ASSOC); */
	$operaion = $pdoread -> prepare("SELECT user_logins.`userid`, user_logins.`password`, user_logins.`username`, user_logins.`emailid`, user_logins.`role`, user_logins.`desgination`, user_logins.`department`, user_logins.`status`, user_logins.`cost_center`,branch_master.display_name,user_logins.`branch`, user_logins.`accesskey`, user_logins.`mobile_accesskey` FROM `user_logins` inner join branch_master on user_logins.cost_center=branch_master.cost_center WHERE user_logins.`status`='Active' and user_logins.`userid`=:userid");
	$operaion->bindParam(':userid', $userid, PDO::PARAM_STR);
	//$fetch->bindParam(':password', $password, PDO::PARAM_STR);
	$operaion -> execute();
	if($operaion->rowCount()>0){
	$operaions = $operaion->fetch(PDO::FETCH_ASSOC);
	
	
	$operaionsname =$operaions['username'];
	$operaionsuserid =$operaions['userid'];
	$operaionsbranch =$operaions['branch'];
	$operaionscostcentercode =$operaions['storeaccess'];
	$operaionsaccesskey =$operaions['mobile_accesskey'];
	$operaionsrole =$operaions['role'];
	$operaionscostcenter =$operaions['cost_center'];
	}else{	
	$operaionsname ='11111';
	$operaionsuserid ='11111';
	$operaionsbranch ='11111';
	$operaionscostcentercode ='11111';
	$operaionsaccesskey ='11111';
	$operaionsrole ='11111';
	$operaionscostcenter ='11111';
		
	}

	$stmt1=$pdoread->prepare("SELECT `display_name` ,`cost_center` FROM `branch_master` where  `cost_center`=:costcenter");
	//$fetch->bindParam(':userid', $userid, PDO::PARAM_STR);
	$stmt1->bindParam(':costcenter',$fetchres['cost_center'], PDO::PARAM_STR);
	$stmt1 -> execute();
	$rows = $stmt1->fetch(PDO::FETCH_ASSOC);
	http_response_code(200);
    $response['error']= false;
    $response['message']= "Data found";
	 $response['profilepic']= $row['image'];
    $response['doctorassistname']= $emisname;
    $response['doctorassistuserid']= $emisuserid ;
    $response['doctorassistbranch']= $emisbranch;
    $response['doctorassistcostcentercode']= $rows['cost_center'];
    $response['doctorassistcostcenter']= $rows['display_name'];
    $response['doctorassistaccesskey']= $emisaccesskey;
    $response['doctorassistrole']= $emisrole;
	
    $response['employeename']= $emisname;
    $response['employeeuserid']= $emisuserid;
    $response['employeebranch']= $emisbranch;
    $response['employeecostcentercode']= $rows['cost_center'];
    $response['employeecostcenter']= $rows['display_name'];
    $response['employeeaccesskey']= $emisaccesskey;
    $response['employeerole']= $emisrole;
	
    $response['iassistname']= $iassistname;
    $response['iassistdesignation']= $iassistdesignation;
    $response['iassistuserid']= $iassistuserid;
    $response['iassistbranch']= $iassistbranch;
    $response['iassistcostcentercode']= $rows['cost_center'];
    $response['iassistcostcenter']= $rows['display_name'];
    $response['iassistaccesskey']= $iassistaccesskey;
    $response['iassistaccesskey']= $iassistaccesskey;
    $response['iassistrole']= $iassistrole;
    $response['iassisttype']= $iassisttype;
    $response['iassistdepartment'] = $iassistdepartment;
					           	   
    $response['poname']= $poname;
    $response['pouserid']= $pouserid;
    $response['pobranch']= $pobranch;
    $response['pocostcentercode']= $rows['cost_center'];
    $response['pocostcenter']= $rows['display_name'];
    $response['poaccesskey']= $poaccesskey;
    $response['porole']= $porole;
	   
    $response['reffmisname']= $reffmisname;
    $response['reffmisdesignation']= $reffmisdesignation;
    $response['reffmisuserid']= $reffmisuserid;
    $response['reffmisbranch']= $reffmisbranch;
    $response['reffmiscostcentercode']= $rows['cost_center'];
    $response['reffmiscostcenter']= $rows['display_name'];
    $response['reffmisaccesskey']= $reffmisaccesskey;
    $response['reffmisrole']= $reffmisrole;
    $response['reffmistype']= $reffmistype;
  
    $response['misname']= $misname;
    $response['misdesignation']= $misdesignation;
    $response['misuserid']= $misuserid;
    $response['misbranch']= $misbranch;
    $response['miscostcentercode']= $rows['cost_center'];
    $response['miscostcenter']= $rows['display_name'];
    $response['misaccesskey']= $misaccesskey;
    $response['misrole']= $misrole;
    $response['mistype']= $mistype;
    $response['mismobile']= $mismobile;

    $response['emisname']= $emisname;
    $response['emidsesignation']= $emidsesignation;
    $response['emisuserid']= $emisuserid;
    $response['emisbranch']= $emisbranch;
    $response['emiscostcentercode']= $rows['cost_center'];
    $response['emiscostcenter']= $rows['display_name'];
    $response['emisaccesskey']= $emisaccesskey;
    $response['emisrole']= $emisrole;
	   
    $response['doctorname']= $emisname;
    $response['doctordesignation']= $emidsesignation;
    $response['doctoruserid']= $emisuserid;
    $response['doctorbranch']= $emisbranch;
    $response['doctorcostcentercode']= $rows['cost_center'];
    $response['doctorcostcenter']= $rows['display_name'];
    $response['doctoraccesskey']= $emisaccesskey;
    $response['doctorrole']= $emisrole;
	  
    $response['securityname']= $emisname;
    $response['securityuserid']=  $emisuserid;
    $response['securitybranch']= $emisbranch;
    $response['securitycostcentercode']= $rows['cost_center'];
    $response['securitycostcenter']= $rows['display_name'];
    $response['securityaccesskey']= $emisaccesskey;
    $response['securityrole']= $emisrole;
	
	
    $response['hrmsname']= $hrmsname;
    $response['hrmsuserid']= $hrmsuserid;
    $response['hrmsbranch']= $hrmsbranch;
    $response['hrmscostcentercode']= $rows['cost_center'];
    $response['hrmscostcenter']= $rows['display_name'];
    $response['hrmsaccesskey']= $hrmsaccesskey;
    $response['hrmsrole']= $hrmsrole;
    $response['hrmsreportingto']= $hrmsreportingto;
    $response['hrmsreportingofficer']= $hrmsreportingofficer;
    $response['hrmsdepartment']= $hrmsdepartment;

    $response['operationsname']= $operaionsname;
    $response['operationsuserid']= $operaionsuserid;
    $response['operationsbranch']= $operaionsbranch;
    $response['operationscostcentercode']= $operaions['cost_center'];
    $response['operationscostcenter']= $operaions['display_name'];
    $response['operationsaccesskey']= $operaionsaccesskey;
    $response['operationsrole']= $operaionsrole;

    $response['dynamicformname']= $emisname;
    $response['dynamicformuserid']= $emisuserid;
    $response['dynamicformbranch']= $emisbranch;
    $response['dynamicformcostcentercode']= $rows['cost_center'];
    $response['dynamicformcostcenter']= $rows['display_name'];
    $response['dynamicformaccesskey']= $emisaccesskey;
    $response['dynamicformrole']= $emisrole;
	
	$response['permissionsname']= $emisname;
    $response['permissionsuserid']= $emisuserid;
    $response['permissionsbranch']= $emisbranch;
    $response['permissionscostcentercode']= $rows['cost_center'];
    $response['permissionscostcenter']= $rows['display_name'];
    $response['permissionsaccesskey']= $emisaccesskey;
    $response['permissionsrole']= $emisrole;
	
	$response['getdataname']= $emisname;
    $response['getdatauserid']= $emisuserid;
    $response['getdatabranch']= $emisbranch;
    $response['getdatacostcentercode']= $rows['cost_center'];
    $response['getdatacostcenter']= $rows['display_name'];
    $response['getdataaccesskey']= $emisaccesskey;
    $response['getdatarole']= $emisrole;
	
	$response['journelname']=$emisname;
    $response['journeluserid']= $emisuserid;
    $response['journelbranch']= $emisbranch;
    $response['journelcostcentercode']= $rows['cost_center'];
    $response['journelcostcenter']= $rows['display_name'];
    $response['journelaccesskey']= $emisaccesskey;
    $response['journelrole']= $emisrole;

    $response['pacname ']=$emisname;
    $response['pacuserid']=$emisuserid;
    $response['pacbranch']= $emisbranch;
    $response['paccostcentercode']= $rows['cost_center'];
    $response['paccostcenter']= $rows['display_name'];
    $response['pacaccesskey']= $emisaccesskey;
    $response['pacrole']=$emisrole;

    $response['myarticalname']=$emisname;
    $response['myarticaluserid']=$emisuserid;
    $response['myarticalbranch']= $costcenter;
    $response['myarticalcostcentercode']= $rows['cost_center'];
    $response['myarticalcostcenter']= $rows['display_name'];
    $response['myarticalaccesskey']=$emisaccesskey;
    $response['myarticalrole']= $emisrole;

    $response['clinicalnewsname ']= $emisname ;
    $response['clinicalnewsuserid']= $emisuserid;
    $response['clinicalnewsbranch']= $costcenter;
    $response['clinicalnewscostcentercode']= $rows['cost_center'];
    $response['clinicalnewscostcenter']= $rows['display_name'];
    $response['clinicalnewsaccesskey']=$emisaccesskey;
    $response['clinicalnewsrole']=$emisrole;
	
	$response['dashboardname']= $emisname ;
    $response['dashboardid']= $emisuserid;
    $response['dashboardbranch']=$costcenter;
    $response['dashboardcostcentercode']= $rows['cost_center'];
    $response['dashboardcostcenter']= $rows['display_name'];
    $response['dashboardaccesskey']= $emisaccesskey;
    $response['dashboardrole']= $emisrole;
	
    $response['internationalname']= $emisname ;
    $response['internationaluserid']= $emisuserid;
    $response['internationalbranch']= $costcenter;
    $response['internationalcostcentercode']= $rows['cost_center'];
    $response['internationalcostcenter']= $rows['display_name'];
    $response['internationalaccesskey']= $emisaccesskey;
    $response['internationalrole']=  $emisrole;
	
}else{
	http_response_code(503);
    $response['error']= true;
    $response['message']= "Access denied";
}
}else{
	http_response_code(400);
    $response['error']= true;
    $response['message']= "Sorry, some details are missing";
}
} catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed".$e;
}
echo json_encode($response);
$pdoread= null;
$pdo1 = null;
$pdo2 = null;
$con = null;
//$himsdemo = null;
$pdo_hrms = null;
?>