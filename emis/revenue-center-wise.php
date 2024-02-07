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
$tdate=$data->date;
$dates = date('Y-m-d', strtotime($tdate)); 

$response = array();
$response1 = array();
$response2 = array();
$response3 = array();
if(!empty($accesskey) && !empty($tdate)){

if(date("Y-m-d") > $dates){
    $realdates=$dates;
}else{
   
      $realdates=date("Y-m-d", strtotime($data->date));
}

 $check = $pdoread -> prepare("SELECT `userid`,`username`,mobile_accesskey AS `accesskey`,`branch` AS storeaccess FROM `user_logins` WHERE `mobile_accesskey`=:accesskey AND `status`= 'Active'");
$check -> bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$check -> execute();
if($check->rowCount() >0){
$datesql =$pdoread->prepare("SELECT (SELECT DATE_FORMAT(:realdates,'Unit Wise Target Vs Achievement As On %d-%b, %Y  - INR MN')) AS datef,(SELECT DATE_FORMAT(:realdates,'%b-%y Ach')) AS ach,(SELECT DATE_FORMAT(:realdates,'%b-%y Ach %')) AS achp,(SELECT DATE_FORMAT(DATE_SUB(:realdates, INTERVAL 1 MONTH),'as on date %b-%y Ach')) AS Acho");
$datesql->bindParam(":realdates", $realdates, PDO::PARAM_STR);
$datesql->execute();
$ressql = $datesql->fetch(PDO::FETCH_ASSOC);

$response1['title']=$ressql['datef'];
$response1['sno']="S NO";
$response1['branch']="Location";
$response1['target']="Target";
$response1['achieved']=$ressql['ach'];
$response1['achievedpercentage']=$ressql['achp'];
$response1['asondate']=$ressql['Acho'];

$Sql = "SELECT DISTINCT `branch`,`target`,cost_center FROM `revenue_target`";
$result = $medicovermis->prepare($Sql);  
$result->execute();
while($row = $result->fetch(PDO::FETCH_ASSOC)) {
$bran=$row['branch'];
$target=$row['target'];
$cost_center=$row['cost_center'];

	$sql1 = $pdoread->prepare("SELECT (SELECT IFNULL(ROUND((SUM(`total`)/1000000),2),0) FROM `revenue_breakup` WHERE `cost_center` = :bran AND Date(date) between Date_format(:realdates,'%Y-%m-1') AND :realdates) AS Ach,(SELECT IFNULL(ROUND((((SUM(`total`)/1000000)/:target)*100),2),0) FROM `revenue_breakup` WHERE `cost_center` = :bran AND Date(date) between Date_format(:realdates,'%Y-%m-1') AND :realdates ) AS Achp,(SELECT IFNULL(ROUND((SUM(`total`)/1000000),2),0) FROM `revenue_breakup` WHERE `cost_center` = :bran AND Date(date) = DATE_SUB(:realdates, INTERVAL 1 MONTH)) AS acho;");
	
	$sql1->bindParam(":target",$target, PDO::PARAM_STR);
	$sql1->bindParam(":realdates", $realdates, PDO::PARAM_STR);
	$sql1->bindParam(":bran", $cost_center, PDO::PARAM_STR);
    $sql1->execute();






$row2 =$sql1->fetch(PDO::FETCH_ASSOC);



$Sql4 =$pdo4->prepare("UPDATE `revenue_target` SET `target`=:targets,`Ach`=:Ach,`Achp`=:Achp,`Acho`=:acho WHERE `branch` = :bran");

	$Sql4->bindParam(":targets",$target, PDO::PARAM_STR);
	$Sql4->bindParam(":Ach", $row2['Ach'], PDO::PARAM_STR);
	$Sql4->bindParam(":Achp", $row2['Achp'], PDO::PARAM_STR);
	$Sql4->bindParam(":acho", $row2['acho'], PDO::PARAM_STR);
	$Sql4->bindParam(":bran", $bran, PDO::PARAM_STR);
    $Sql4->execute();



$CHECK = $row2['acho'];
$ach = $ach+$row2['Ach'];
$achp = $achp+$row2['Achp'];
$acho = $acho+$row2['acho'];
$targettotal = $targettotal+$target;


	 }

     $gapcount=$admcount-$plancount;
     $Sql5 =$pdo4->prepare("UPDATE `revenue_target` SET `target`=:targettotal,`Ach`=:Ach,`Achp`=:Achp,`Acho`=:acho WHERE `branch` ='Total'");
	 
	 
	 $Sql5->bindParam(":targettotal", $targettotal, PDO::PARAM_STR);
	 $Sql5->bindParam(":Ach", $ach, PDO::PARAM_STR);
	$Sql5->bindParam(":Achp", $achp, PDO::PARAM_STR);
	$Sql5->bindParam(":acho", $acho, PDO::PARAM_STR);
    $Sql5->execute();
     
     $mysal=$pdoread->prepare("SELECT `branch`,`target`,`Ach`,`Achp`,`Acho`,cost_center FROM `revenue_target` WHERE `branch` != 'Total'  AND `status` = 'Active' ORDER BY `Achp` DESC");
	  $mysal->execute();
     $im=1;
     $sno = 1;
      while($results =$mysal->fetch(PDO::FETCH_ASSOC)) {
            $ach1=$results['Achp'];
    
          if($ach1>=0 && $ach1<30){
             $colorcode='#F1948A';
              }else if($ach1>=30 && $ach1<50){
              $colorcode='#E59866';
           }else if($ach1>=50 && $ach1<80){
           $colorcode='#F0B27A';
           }else if($ach1>=80 && $ach1<100){
           $colorcode='#F7DC6F';
           }else if($ach1>=100){
             $colorcode='#82E0AA';
              }else{
             $colorcode='#eb8681';
                  }
                  $bb='';
 
		     $bb=$results['branch'];
	
               $temp=[
                'sno'=>$sno,
				  'branchcode'=>$results['cost_center'],
             'branch'=>$bb,
             'target'=>$results['target'],
             'achieved'=>$results['Ach'],
             'achievedpercentage'=>$results['Achp'],
             'asondate'=>$results['Acho'],
             'branchcode'=>$results['cost_center'],
             'colorcode'=>$colorcode
             ];
                   
         $sno++;         
        array_push($response, $temp);  
      }
      $my=$pdoread->prepare("SELECT ROUND(SUM(`target`),2) AS target,ROUND(SUM(`Ach`),2) AS Ach,ROUND((SUM(`Ach`) / SUM(`target`))*100,2) AS Achp,ROUND(SUM(`Acho`),2) AS Acho FROM `revenue_target` WHERE `branch` != 'Total' AND `status` = 'Active' ORDER BY `Achp` DESC");
      $my->execute();
      $mytot=$my->fetch(PDO::FETCH_ASSOC);
        
          $achp1 = $mytot['Achp'];
          if($achp1>=0 && $achp1<30){
             $colorcode='#F1948A';
              }else if($achp1>=30 && $achp1<50){
              $colorcode='#E59866';
           }else if($achp1>=50 && $achp1<80){
           $colorcode='#F0B27A';
           }else if($achp1>=80 && $achp1<100){
           $colorcode='#F7DC6F';
           }else if($achp1>=100){
             $colorcode='#82E0AA';
              }else{
             $colorcode='#eb8681';
                  }
          $temp1=[
             'sno'=>'Total',
             'branch'=>'Total',
             'branchcode'=>'Total',
             'target'=>$mytot['target'],
             'achieved'=>$mytot['Ach'],
             'achievedpercentage'=>$mytot['Achp'],
             'asondate'=>$mytot['Acho'],
             'colorcode'=>$colorcode
             ];  
        array_push($response, $temp1); 
       $response1['list']=$response;
	     http_response_code(200);
	   $response2['error'] = false;
	   $response2['message']="Data Found";
       $response2['data'] = $response1;	   
       }else{
	http_response_code(400);
	$response2['error'] = true;
	$response2['message']="Access denied!";
  }     
}else{
  http_response_code(400);
  $response2['error'] = true; 
  $response2['message']= "Some details are missing!";

}
      echo json_encode($response2);
unset($pdoread);
unset($pdo4);
unset($medicovermis);
          ?>