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
$userid=$data->userid;
$response = array();
$response1= array();
try{
if(!empty($accesskey)){
$check =$pdoread->prepare("SELECT `userid`,`branch`,`cost_center`,username,role,androidpermissions,androidsubmenu,androiddashboard FROM `super_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check->execute();
if($check->rowCount()>0){
$result= $check->fetch(PDO::FETCH_ASSOC);

$select=$pdoread->prepare("SELECT `sno`, `userid`, `password`, `username`, `dob`, `emailid`,`mobile`, `role`, `desgination`, `department`, `sp_code`, `shortcutkey_id`, `createdon`,`createdby`, `modifiedon`, `modifiedby`, `status`, `cost_center`, `branch`, `otp`,`accesskey`, `version`, `model`, `udid`, `tokenid`, `mobile_accesskey`, `lastlogin`,`androidpermissions`, `androidsubmenu`, `androiddashboard`, `stockpoints`, `substockpoint`,`gs_dept`,`status_check` FROM `super_logins` WHERE `userid`=:userid and `status` = 'Active' order by `sno` desc");
$select->bindParam(":userid",$userid,PDO::PARAM_STR);
$select->execute();
 if($select->rowCount()>0){
while($result2=$select->fetch(PDO::FETCH_ASSOC)){
$temp=[
     "sno"=>$result2['sno'],
     "userid"=>$result2['userid'],
     "username"=>$result2['username'],
     "emailid"=>$result2['emailid'],
     "mobile"=>$result2['mobile'],
     "role"=>$result2['role'],
     "desgination"=>$result2['desgination'],
     "department"=>$result2['department'],
     "sp_code"=>$result2['sp_code'],
     "shortcutkey_id"=>$result2['shortcutkey_id'],
     "createdby"=>$result2['createdby'],
     "createdon"=>$result2['createdon'],
     "modifiedby"=>$result2['modifiedby'],
     "modifiedon"=>$result2['modifiedon'],
     "status"=>$result2['status'],
     "cost_center"=>$result2['cost_center'],
     "branch"=>$result2['branch'],
     "stockpoints"=>$result2['stockpoints'],
     "substockpoint"=>$result2['substockpoint'],
     "gs_dept"=>$result2['gs_dept'],
     ];
     array_push($response1,$temp);      
     }
    http_response_code(200);
    $response['error']=false;
    $response['message']='Data Found';
    $response['empdetailslist']=$response1;
}   
else{
    http_response_code(503);
    $response['error']=true;
    $response['message']='No data Found'; 
}
}else{
    http_response_code(400);
    $response['error']=true;
    $response['message']='Access denied!';
}
}else{
    http_response_code(400);
    $response['error']=true;
    $response['message']='Sorry! some details are missing';
}
}catch(PDOEXCEPTION $e){
    http_response_code(503);
    $response['error']=true;
    $response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response,true);
unset($pdoread);
?>
