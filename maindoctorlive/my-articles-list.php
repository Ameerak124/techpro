<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
//header_remove('Server');
include "pdo-db.php";
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
$response = array();
try
{
if(!empty($accesskey)){
$accesscheck ="SELECT `userid`,`branch`,`cost_center`,username,role,androidpermissions,androidsubmenu,androiddashboard FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
$stmt = $pdoread->prepare($accesscheck);
$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$stmt->execute();
if($stmt->rowCount()>0){
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$select=$pdoread->prepare("SELECT `sno`,if(`date`='null','',date_format(`date`,'%d-%b-%Y')) as date,`unit`, `content_text`,if(image='Please select an image file to upload.','',concat(:baseurl,'/',`image`)) as image, `imagefilename`, `unique_id`,if (`created_on`='0000-00-00 00:00:00','',date_format(`created_on`,'%d-%b-%Y %h:%i')) AS authorized_on, `created_by` as authorized_by,authorized_name,if (modified_on='0000-00-00 00:00:00','',date_format( `modified_on`,'%d-%b-%Y %h:%i')) AS modifiedon, `modified_by`, `status`,concat(:baseurl,'/images/gallery_icon.png') AS galleryicon , concat(:baseurl,'/images/share_img.png') as shareicon FROM `my_articles` WHERE `status`='Active'  AND created_by = :userid order by sno desc");
$select->bindParam(':baseurl', $baseurl, PDO::PARAM_STR);
$select->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
$select->execute();
if($select->rowCount()>0){
    $resultt=$select->fetchAll(PDO::FETCH_ASSOC);
    http_response_code(200);
    $response['error']=false;
    $response['message']='Data Found';   
    $response['date']='Date';
    $response['authorized_by']='Authorized By';
    $response['share']='Share';
    $response['view_document']='View Document';
    $response['unit']='Unit';
    $response['content']='Content';
    $response['supporting_document']='Upload Files(Optional)';
    $response['myarticleslist']=$resultt;
}   
else{
    http_response_code(503);
    $response['error']=true;
    $response['message']='No data Found'; 
    $response['date']='Date';
    $response['authorized_by']='Authorized By';
    $response['share']='Share';
    $response['view_document']='View Document';
    $response['unit']='Unit';
    $response['content']='Content';
    $response['supporting_document']='Upload Files(Optional)';
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
    $response['message']= "Connection failed: ".$e->getMessage();
}
echo json_encode($response,true);
unset($pdoread)
?>
