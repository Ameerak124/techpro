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
$fromdate =$_GET['fromdate'];
$response =array();
try {
$query=$pdoread->prepare("Select (SELECT count(DISTINCT `admissionno`) FROM `registration` WHERE Date(`admittedon`) = :fromdate AND cost_center='MCBEG' AND NOT `organization_name` LIkE '%Medicover Associate%' AND NOT `organization_name` LIkE '%MEDICOVER HOSPITAL%' AND NOT `organization_name` LIkE '%MEDICOVER CONSULTANT%' AND `admissionstatus`!='Cancelled' AND NOT `admission_type` LIKE '%DIALY%') AS ad,(SELECT count(DISTINCT `admissionno`) FROM `registration` WHERE Date(`admittedon`) BETWEEN DATE_FORMAT(:fromdate,'%Y-%m-01') AND :fromdate AND `cost_center`='MCBEG' AND NOT `organization_name` LIkE '%Medicover Associate%' AND NOT `organization_name` LIkE '%MEDICOVER HOSPITAL%' AND NOT `organization_name` LIkE '%MEDICOVER CONSULTANT%' AND `admissionstatus`!='Cancelled' AND `admissionstatus`!='Hold' AND NOT `admission_type` LIKE '%DIALY%') AS mtd;");
	
$query->bindParam(':fromdate', $fromdate, PDO::PARAM_STR);
$query->execute();

if($query->rowCount()>0){
	http_response_code(200);
 $response['error']=false;
 $response['message']="Data found";
 while($res=$query->fetch(PDO::FETCH_ASSOC)){
    	$response['adm'] = $res['ad'];
    	$response['mtd'] = $res['mtd'];          
 }
}else{
	http_response_code(503);
$response['error']=true;
$response['message']='No Data Found';
}
}catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= "Connection failed: " . $e->getMessage();
}
echo json_encode($response);
$pdoread=null;
?>