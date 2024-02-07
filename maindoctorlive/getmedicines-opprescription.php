<?php
header("Content-Type: application/json; charset=UTF-8");
header("Content-Security-Policy: default-src 'none';");
header("X-Frame-Options: SAMEORIGIN");
header("x-content-type-options: nosniff");
header("Referrer-Policy: same-origin");
header("Strict-Transport-Security: max-age=31536000;");
header("Permissions-Policy: geolocation=(self)");
header_remove('X-Powered-By');
try {
//data credentials
include 'pdo-db.php';
include "pdo-masters.php";
//data credential
$data = json_decode(file_get_contents("php://input"));
$accesskey = $data->accesskey;
$productsearch = $data->productsearch;



$pdo_master = new PDO("mysql:host=$master_servername;dbname=$master_database;charset=utf8", $master_username, $master_password);
// set the PDO error mode to exception
$pdo_master ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if(!empty($accesskey) && !empty($productsearch)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid`, `cost_center` ,`username` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check -> rowCount() > 0){
	$result = $check->fetch(PDO::FETCH_ASSOC);
	$userid = $result['userid'];
	$username = $result['username'];
	$cost_center = $result['cost_center'];  


	/* get stockpoint code start */
	$getspcode = $pdo_master -> prepare("SELECT `stockpoints`.`code` FROM `stockpoints` WHERE `stockpoints`.`is_active` = '1' AND `stockpoints`.`branchcode` = :costcenter AND `stockpoints`.`is_oppharmacy` = '1'");
	$getspcode  -> bindParam(':costcenter', $cost_center, PDO::PARAM_STR);
	$getspcode -> execute();
	if($getspcode -> rowCount() > 0){
		$spdata = $getspcode -> fetch(PDO::FETCH_ASSOC);
		$spcode = $spdata['code'];

			/* get items data start */
		$getitems = $pdoread -> prepare("SELECT `itemmaster`.`itemcode` , `itemmaster`.`itemname`, IFNULL(inv.avlqty,0) AS avlqty FROM `itemmaster` LEFT JOIN (SELECT `stockpoint_inventory`.`itemcode` , `stockpoint_inventory`.`itemname` , SUM(IF(DATE(`stockpoint_inventory`.`exp_date`) > DATE_ADD(CURRENT_DATE , INTERVAL 30 DAY),`stockpoint_inventory`.`quantity`,0)) AS avlqty FROM `stockpoint_inventory` WHERE `stockpoint_inventory`.`spcode` = :spcode   GROUP BY `stockpoint_inventory`.`itemcode`) AS inv  ON inv.itemcode = `itemmaster`.`itemcode` WHERE `itemmaster`.`itemname` LIKE :itemname LIMIT 15");
		$getitems -> bindValue(':itemname', "%{$productsearch}%", PDO::PARAM_STR);
		$getitems -> bindParam(':spcode', $spcode, PDO::PARAM_STR);
		$getitems -> execute();
		if($getitems -> rowCount() > 0){
			http_response_code(200);
			$response['error'] = false;
			$response['message'] = 'Data Found!';
			$response['itemslist']  = $getitems -> fetchAll(PDO::FETCH_ASSOC);

 		}
		else
		{
			http_response_code(503);
			$response['error'] = true;
			$response['message'] = 'No Data Found!';
		}
			/* get items data end */
	}
	else{
		http_response_code(503);
		$response['error'] = true;
		$response['message'] = 'Something went wrong!';
	}
	/* get stockpoint code end */
	
}
else
{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access denied!";
}
}
else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
}
} 
catch(PDOException $e) {
	http_response_code(200);
	$response['error'] = true;
	$response['message']= $e->getMessage();
	
}
echo json_encode($response);
$pdoread = null;
$pdo_master = null;
?>


