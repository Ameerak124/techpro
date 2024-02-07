<?php
header("Content-Type: application/json; charset=UTF-8");
try {
//data credentials
include 'pdo-db.php';
//data credential
$term = $_POST['keyword'];
$accesskey = $_POST['ack'];
$admsno = $_POST['admsno'];

$pdoread = new PDO("mysql:host=$servername;dbname=$database;charset=utf8", $username, $password);
// set the PDO error mode to exception
$pdoread->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//echo "Connected successfully";
if(!empty($accesskey)){
//Check access 
$check = $pdoread -> prepare("SELECT `userid` FROM `user_logins` WHERE `accesskey` = :accesskey AND `status` = 'Active'");
$check->bindParam(':accesskey', $accesskey, PDO::PARAM_STR);
$check -> execute();
$result = $check->fetch(PDO::FETCH_ASSOC);
$empname = $result['userid'];
if($check -> rowCount() > 0){
     //submit_item_query 
     $getitems_query = "SELECT `pharmcy_indent`.`sno` AS pharmsno,`pharmcy_indent`.`hsn` AS hsn,`pharmcy_indent`.`drug_price` AS price, `pharmcy_indent`.`order_no`, `umrno`, `ipno`,  `category`, `sub_category`, `pharmcy_indent`.`drug_code`,`pharmcy_indent`.`drug_code`, `pharmcy_indent`.`drug_name`, `pharmcy_indent`.`hsn`, `pharmcy_indent`.`quantity` FROM `pharmcy_orders` INNER JOIN `pharmcy_indent` ON `pharmcy_orders`.`order_no` = `pharmcy_indent`.`order_no` WHERE `is_accepted` = '1' AND `pharmcy_orders`.`ipno` = :admsno AND `pharmcy_indent`.`drug_name` LIKE :itemname ";   
     $getitems_sbmt = $pdoread -> prepare($getitems_query);
     $getitems_sbmt -> bindParam(":admsno", $admsno, PDO::PARAM_STR);  
     $getitems_sbmt -> bindValue(":itemname", "%{$term}%", PDO::PARAM_STR);  
     $getitems_sbmt -> execute();
     if($getitems_sbmt -> rowCount() > 0){
          $data = $getitems_sbmt -> fetchAll(PDO::FETCH_ASSOC);
          $response['error']= true;
	     $response['message']="Data Found";
          $response['list'] = $data;
     }
     else
     {
          $response['error']= true;
	     $response['message']="No Data Found!";
     }
}
else
{
	$response['error']= true;
	$response['message']="Access Denied! Pleasae try after some time";
}
}
else{
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
     $response['accesskey'] = $accesskey;
}
} 
catch(PDOException $e) {
	$response['error'] = true;
	$response['message']= $e->getMessage();
}
echo json_encode($response);
$pdoread = null;
?>