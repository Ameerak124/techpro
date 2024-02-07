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
$response3 = array();
try{
if(!empty($accesskey) && !empty($tdate)){

 $check = $pdoread -> prepare("SELECT `userid`,`username`,mobile_accesskey AS `accesskey`,`branch` AS storeaccess FROM `user_logins` WHERE `mobile_accesskey`=:accesskey AND `status`= 'Active'");
$check -> bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$check -> execute();




if($check->rowCount() >0){
	$result = $check->fetch(PDO::FETCH_ASSOC);
	date_default_timezone_set('Asia/Kolkata');
$mydate=date("d-M");
$mytime=date("h:i A");
$realdates=date("Y-m-d");
if($dates>=$realdates){
 $myheading=$mydate.' IP Status @ '.$mytime;
 $response1['title']=$myheading;
      
 }else{
 $dates1=date('m-d', strtotime($dates));

    $realdates=$dates;
$new_date = date('d-M-Y', strtotime($realdates));
$myheading=$new_date.' IP Status';
 $response1['title']=$myheading;
}

$sql1="SELECT `cost_center`,`branch`,view_branch FROM `admission_report` WHERE`status`='Active'";
$stmt1 = $pdoread->prepare($sql1);
$stmt1->bindParam(":date", $dates, PDO::PARAM_STR);
$stmt1->execute();
$id	= 1;
$admcount='';
$plancount='';
$achcount='';
 $response1['gapbgcolor']="#ffc7c7";
  $response1['gaptextcolor']="#a30f1e";
     while($row =$stmt1->fetch(PDO::FETCH_ASSOC)) {
         
            $bran=$row['branch'];
            $cost_center=$row['cost_center'];
		

			

$sql3="SELECT `plan` FROM `admissionplan_calendars` WHERE branch=:branch AND `plan`!='0' AND `date`=:date";
$stmt3 = $medicovermis->prepare($sql3);
$stmt3->bindParam(":branch", $bran, PDO::PARAM_STR);
$stmt3->bindParam(":date", $realdates, PDO::PARAM_STR);
$stmt3->execute();	

$row3 =$stmt3->fetch(PDO::FETCH_ASSOC);		

             $Sql1 =$pdoread->prepare("SELECT (SELECT count(DISTINCT `admissionno`) FROM `registration` WHERE Date(`admittedon`)  = :date AND cost_center=:branch AND NOT `organization_name` LIkE '%Medicover Associate%' AND NOT `organization_name` LIkE '%MEDICOVER HOSPITAL%' AND NOT `organization_name` LIkE '%MEDICOVER CONSULTANT%' AND `admissionstatus`!='Cancelled' AND `admissionstatus`!='Hold' AND NOT `admission_type` LIKE '%DIALY%') AS ad,(SELECT count(DISTINCT `admissionno`) FROM `registration` WHERE Date(`admittedon`)  BETWEEN DATE_FORMAT(:date,'%Y-%m-01') AND :date AND cost_center=:branch AND NOT `organization_name` LIkE '%Medicover Associate%' AND NOT `organization_name` LIkE '%MEDICOVER HOSPITAL%' AND NOT `organization_name` LIkE '%MEDICOVER CONSULTANT%' AND `admissionstatus`!='Cancelled' AND `admissionstatus`!='Hold' AND NOT `admission_type` LIKE '%DIALY%') AS mtd");
			 $Sql1->bindParam(":date", $realdates, PDO::PARAM_STR);              
            $Sql1->bindParam(":branch", $cost_center, PDO::PARAM_STR);  
        
		  $Sql1->execute();
         $row2 =$Sql1->fetch(PDO::FETCH_ASSOC);
       

		   $ach=round($row2['ad']/$row3['plan']*100);
           $gap=$row2['ad']-$row3['plan'];
           $adm=$row2['ad'];
           $mtd=$row2['mtd'];
	   
    
	
	     $Sql4="UPDATE `admission_report` SET `plan`=:plan,`adm`=:adm,`ach`=:ach,`gap`=:gap,`mtd` = :mtd WHERE branch=:bran";
         $stmt4 = $pdo4->prepare($Sql4);
         $stmt4->bindParam(":plan", $row3['plan'], PDO::PARAM_STR);
         $stmt4->bindParam(":adm", $adm, PDO::PARAM_STR);
         $stmt4->bindParam(":ach", $ach, PDO::PARAM_STR);
         $stmt4->bindParam(":gap", $gap, PDO::PARAM_STR);
         $stmt4->bindParam(":mtd", $mtd, PDO::PARAM_STR);
         $stmt4->bindParam(":bran", $bran, PDO::PARAM_STR);
         $stmt4->execute();
	
	
               	$id++;
     }

    
     
    
	 
	   $mysal="SELECT view_branch AS branch, `plan`, `adm`, `ach`, `gap`,`mtd` FROM `admission_report` Where branch!='Total' AND `status` = 'Active'  ORDER by  `ach` DESC,`gap` ASC";
         $stmt5 = $pdoread->prepare($mysal);
         $stmt5->execute();
	 
	
	 
	 
     $im=1;
      while($results = $stmt5->fetch(PDO::FETCH_ASSOC)) {
            $ach1=$results['ach'];
    
          if($ach1>=50 && $ach1<75){
             $colorcode='#f5e28c';
              }else if($ach1>=75 && $ach1<90){
              $colorcode='#d496bf';
           }else if($ach1>=90 && $ach1<100){
           $colorcode='#87ceeb';
           }else if($ach1>=100){
             $colorcode='#2bb559';
              }else{
             $colorcode='#eb8681';
                  }
          $bb='';           

		$bb=$results['branch'];

               $temp=[
             'branch'=>$bb,
             'plan'=>$results['plan'],
             'adm'=>$results['adm'],
             'ach'=>$results['ach'],
             'gap'=>$results['gap'],
             'mtd'=>$results['mtd'],
             'colorcode'=>$colorcode
             ];
                   
                  
        array_push($response, $temp); 
    
       
      }

	  
	  
      $my="SELECT SUM(`plan`) As myplan, SUM(`adm`) myadm, SUM(`gap`) As mygap,SUM(`mtd`) AS mtd FROM `admission_report` WHERE branch!='Total' AND `status` = 'Active'";
	   $stmt6 = $pdoread->prepare($my);
       $stmt6->execute();
      $mytot=$stmt6->fetch(PDO::FETCH_ASSOC);
	  http_response_code(200);
	$response1['error'] = false;
	$response1['message']="Data Found";
	 	if($mytot['myplan']!='0'){
	  $ach3=round(($mytot['myadm']/$mytot['myplan'])*100);
	}else{
		$ach3="0";
	}
	  
          $temp1=[
             'branch'=>'Total',
             'plan'=>$mytot['myplan'],
             'adm'=>$mytot['myadm'],
             'ach'=>(string)$ach3,
             'gap'=>$mytot['mygap'],
             'mtd'=>$mytot['mtd'],
             'colorcode'=>'#385ea4'
             ];  
        array_push($response, $temp1); 
     $response1['admlist']=$response;  
 }else{
	http_response_code(400);
	$response1['error'] = true;
	$response1['message']="Access denied!";
  }     
}else{
  http_response_code(400);
  $response1['error'] = true; 
  $response1['message']= "Some details are missing!";

}
	  
	  
      echo json_encode($response1);
	  
	  
}catch(PDOEXCEPTION $e){
	echo $e->getMessage();
}
	  
      unset($pdoread);
      unset($pdo4);
      unset($pdmedicovermis);

          ?>