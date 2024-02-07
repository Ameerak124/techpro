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
$accesskey = $data->accesskey;
/* $id = $data->id; */
$department = $data->department;
$key_no = $data->key_no;
$no_of_key = $data->no_of_key;
$taken_by_userid = $data->taken_by_userid;
$security_signatureattach = $data->security_signatureattach;
$security_signatureattachfilename=$data->security_signatureattachfilename;
$decodedsecurity_signatureattachimage=base64_decode($security_signatureattach);
$digital_signatureattach = $data->digital_signatureattach;
$digital_signatureattachfilename=$data->digital_signatureattachfilename;
$decodeddigital_signatureattachimage=base64_decode($digital_signatureattach);

$response = array();
try{

 if(!empty($accesskey) && !empty($taken_by_userid)){
$accesscheck ="SELECT `userid`,`branch`,`cost_center`,username,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount() > 0){
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

     $fetch = $pdo1 -> prepare("SELECT `id`, `department`, `key_no`, `no_of_key`, `out_time`, `in_time`, `taken_by_userid`,`status` FROM `key_reg` WHERE `key_no`=:key_no and `status`='Exit' and date(created_on)=CURRENT_DATE");
	$fetch->bindParam(':key_no', $key_no, PDO::PARAM_STR);
	$fetch -> execute();
	if($fetch->rowCount()> 0){
	$fetchres = $fetch->fetch(PDO::FETCH_ASSOC);

   http_response_code(503);
             $response['error'] = true; 
             $response['message']= "This key number already  Exit";
        


 }else{
          

     $result2 = $pdo1-> prepare("SELECT Concat('KRG',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m'),LPAD((SUBSTRING_INDEX(COALESCE(MAX(`id`),'KRG26090000'),Concat('KRG',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m')),-1)+1),'5','0')) AS id FROM `key_reg` where id like concat('%KRG',DATE_FORMAT(CURRENT_DATE,'%y'),'%') LIMIT 1");
     $result2->execute();
     $data=$result2->fetch(PDO::FETCH_ASSOC);
     $key_id=$data['id'];
      
     # signatureattachfile query #

     $time=date("Y-m-d-H-i-s");		
	if(!empty($security_signatureattachfilename)){
		$documenttype = 'security_signatureattachfilename';
		$filename = $security_signatureattachfilename;
		$fileType = pathinfo($security_signatureattachfilename, PATHINFO_EXTENSION);
		$fileName = basename($signatureattachfilename);
		$filesize = decodedsecurity_signatureattachimage["size"];
		$filepath = decodedsecurity_signatureattachimage['tmp_name'];
		if($fileType=='PDF' or $fileType=='pdf'){
			$security_signatureattach = pdfupload($filename,$filesize,$key_id,$time,$documenttype,$decodedsecurity_signatureattachimage);
		 } else {
			
		     $security_signatureattach = imageupload($filename,$key_id,$time,$documenttype,$decodedsecurity_signatureattachimage);	 
		 }	
		
		}else{ 
               $security_signatureattach = 'Please select an image file to upload.'; 
          } 

          # security_signatureattachfile query #

          if(!empty($security_signatureattachfilename)){ 
          $documenttype = 'digital_signatureattach';
          $filename = $digital_signatureattachfilename;
          $fileType = pathinfo($digital_signatureattachfilename, PATHINFO_EXTENSION);
          $fileName = basename($digital_signatureattachfilename);
          $filesize = decodeddigital_signatureattachimage["size"];
          $filepath = decodeddigital_signatureattachimage['tmp_name'];
          if($fileType=='PDF' or $fileType=='pdf'){
          $digital_signatureattach = pdfupload($filename,$filesize,$key_id,$time,$documenttype,$decodeddigital_signatureattachimage);
          } else {         
          $digital_signatureattach = imageupload($filename,$key_id,$time,$documenttype,$decodeddigital_signatureattachimage);
          }	
          }else{ 
          $digital_signatureattach = 'Please select an image file to upload.'; 
          } 



     $result = $pdo1 -> prepare("INSERT INTO `key_reg`(`id`, `department`, `key_no`, `no_of_key`, `out_time`, `taken_by_userid`, `taken_security_signature`, `taken_digital_signature`, `created_by`, `created_on`, `status`) VALUES 
	(:id,:department,:key_no,:no_of_key,CURRENT_TIME,:taken_by_userid,:taken_security_signature,:taken_digital_signature,:userid,CURRENT_TIMESTAMP,'Exit')");
	$result->bindParam(':id', $key_id, PDO::PARAM_STR);    
	$result->bindParam(':department', $department, PDO::PARAM_STR);
	$result->bindParam(':key_no', $key_no, PDO::PARAM_STR);
	$result->bindParam(':no_of_key', $no_of_key, PDO::PARAM_STR);
	$result->bindParam(':taken_by_userid',$taken_by_userid, PDO::PARAM_STR);
	// $result->bindParam(':signatureattach', $signatureattach, PDO::PARAM_STR);
	$result->bindParam(':taken_security_signature', $security_signatureattach, PDO::PARAM_STR);
	$result->bindParam(':taken_digital_signature', $digital_signatureattach, PDO::PARAM_STR);
	$result->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
	$result-> execute();


     $result1 = $pdo1 -> prepare("INSERT INTO `keyreg_log`(`id`, `department`, `taken_by_userid`, `key_no`, `no_of_key`,`created_by`, `created_on`, `status` ) VALUES (:id,:department,:taken_by_userid,:key_no,:no_of_key,:userid,CURRENT_TIMESTAMP,'Exit')");
	$result1->bindParam(':id', $key_id, PDO::PARAM_STR);
	$result1->bindParam(':department', $department, PDO::PARAM_STR);
	$result1->bindParam(':taken_by_userid', $taken_by_userid, PDO::PARAM_STR);
	$result1->bindParam(':key_no', $key_no, PDO::PARAM_STR);
	$result1->bindParam(':no_of_key', $no_of_key, PDO::PARAM_STR);
	$result1->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
	$result1-> execute();
     if($result->rowCount()>0){
	   http_response_code(200);
        $response['error'] = false; 
        $response['message']= "Data inserted";
        }else{
              http_response_code(503);
              $response['error'] = true;
              $response['message']="Data not inserted";
        }
 }
        }else{
             http_response_code(400);
             $response['error'] = true;
             $response['message']="Access denied!";
       }  
       }else{
             http_response_code(400);
             $response['error'] = true;
             $response['message'] ="Sorry! Some details are missing";
       }
       } 
             catch(PDOException $e)
       {
             die("ERROR: Could not connect. " . $e->getMessage());
       }


       function compressImage($source, $destination, $quality) { 
          // Get image info 
          $imgInfo = getimagesize($source); 
          $mime = $imgInfo['mime']; 
          
          // Create a new image from file 
          switch($mime){ 
             case 'image/jpg': 
                  $image = imagecreatefromjpeg($source); 
                 imagejpeg($image, $destination, $quality);
                  break; 
                  case 'image/JPG': 
                  $image = imagecreatefromJPEG($source); 
                 imagejpeg($image, $destination, $quality);
                  break;
                  case 'image/jpeg': 
                  $image = imagecreatefromjpeg($source); 
                 imagejpeg($image, $destination, $quality);
                  break; 
                  case 'image/JPEG': 
                  $image = imagecreatefromJPEG($source); 
                 imagejpeg($image, $destination, $quality);
                  break; 
                  case 'image/png': 
                  $image = imagecreatefrompng($source); 
                  imagepng($image, $destination, $quality);
                  break; 
                  case 'image/PNG': 
                  $image = imagecreatefromPNG($source); 
                  imagepng($image, $destination, $quality);
                  break;
                  case 'image/gif': 
                  $image = imagecreatefromgif($source); 
                  imagegif($image, $destination, $quality);
                  break; 
                  case 'image/GIF': 
                  $image = imagecreatefromGIF($source); 
                  imagegif($image, $destination, $quality);
                  break;
                     
              default: 
                  $image = imagecreatefromjpeg($source); 
                 imagejpeg($image, $destination, $quality);
          } 
           
           
          // Return compressed image 
          return $destination; 
      }
      function pdfupload($filename,$filesize,$key_id,$time,$documenttype,$filepath){
           $fileType = pathinfo($filename, PATHINFO_EXTENSION); 
      if ($filesize < 500000)//500000 = 500kb
          {
                
             //$location1 = "documents/images/".$dependent_name;
             $location = "documents/images/".$key_id;
            
          
                     if(!is_dir($location)){
             mkdir($location, 0755);
           }
             $location_upload .=  $location."/".$time."-".$documenttype.".".$fileType;
            // $location1_file .=  $location."/".$time."-".$documenttype.".".$fileType;
                     //move_uploaded_file($filepath,$location);
                      file_put_contents($location_upload,$filepath);
                     
                     //move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $targetfolder_Appointment); 
                $statusMsg = $location_upload;		
           }else{ 
                  $statusMsg = 'Invalid PDF File'; 
              }
                return $statusMsg;
      }
      function imageupload($filename,$key_id,$time,$documenttype,$filepath){
           
                     $fileType = pathinfo($filename, PATHINFO_EXTENSION); 
      
                     $allowTypes = array('jpg','png','jpeg','gif','PNG','JPG','JPEG','GIF'); 
                     if(in_array($fileType, $allowTypes)){ 
                  
                     if(!is_dir($uploadPath)){
                     mkdir($uploadPath, 0755);
                          }
            
                     $imageUploadPath1 .= "documents/images/".$time."-".$key_id."-".$documenttype.".".$fileType;
                     file_put_contents($imageUploadPath1,$filepath);               
                     $statusMsg =$imageUploadPath1;		
              }else{ 
                     $statusMsg = 'Invalid image file'; 
              }
                     return $statusMsg;
              } 
                     echo json_encode($response,true);
                     unset($pdoread);
                     unset($pdo1);
      
      ?>