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
$imageattach1 = $data->imageattach;
$imageattachfilename=$data->imageattachfilename;
$decodedimageattachimage = base64_decode($imageattach1);
$response = array();
try{
	
if(!empty($accesskey) && !empty($imageattach1) && !empty($imageattachfilename) ){
	$accesscheck ="SELECT `userid`,`branch`,`cost_center`,username,role,androidpermissions,androidsubmenu,androiddashboard FROM `super_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	
	if($stmt->rowCount() > 0){
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$userid = $row['userid'];

 

$time=date("Y-m-d-H-i-s");

		 
	if(!empty($imageattachfilename)){ 
        
		 $documenttype = 'imageattach';
		$filename = $imageattachfilename;
		 $fileType = pathinfo($imageattachfilename, PATHINFO_EXTENSION); 
		
		$fileName = basename($imageattachfilename);
		$filesize = decodedimageattachimage["size"];
		$filepath = decodedimageattachimage['tmp_name'];
		if($fileType=='PDF' or $fileType=='pdf'){
			$imageattach = pdfupload($filename,$filesize,$transid,$time,$documenttype,$decodedimageattachimage);
		 } else {
			
				$imageattach = imageupload($filename,$userid,$time,$documenttype,$decodedimageattachimage);
			 
		 }
    }else{ 
        $imageattach = 'Please select an image file to upload.'; 
    }  






	$stmt1 = $pdo4 -> prepare("UPDATE `super_logins` set emp_image=:imageattach where userid=:userid and status='Active'");
     $stmt1 -> bindParam(":userid", $userid, PDO::PARAM_STR);
     $stmt1 -> bindParam(":imageattach", $imageattach, PDO::PARAM_STR);
     $stmt1 -> execute();
	
	 
	 
     if($stmt1 -> rowCount() > 0){
		 http_response_code(200);
          $response['error']= false;
	      $response['message']="Submitted successfully";
     }
		
     else
     {
		 http_response_code(503);
        $response['error']= true;
	     $response['message']="Sorry Something Went Wrong";
     }
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Access Denied!";
}
	
}else{
	http_response_code(400);
	$response['error']= true;
	$response['message']="Sorry! some details are missing";
     
}
} 
catch(PDOException $e) {
	http_response_code(503);
	$response['error'] = true;
	$response['message']= $e->getMessage();
     
     //"Connection failed: " . $e->getMessage();
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
function pdfupload($filename,$filesize,$userid,$time,$documenttype,$filepath){
	$fileType = pathinfo($filename, PATHINFO_EXTENSION); 
if ($filesize < 500000)//500000 = 500kb
    {
		
       //$location1 = "documents/images/".$dependent_name;
       $location = "documents/profiles/".$userid;
	 
    
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
function imageupload($filename,$userid,$time,$documenttype,$filepath){
	
	$fileType = pathinfo($filename, PATHINFO_EXTENSION); 

			$allowTypes = array('jpg','png','jpeg','gif','PNG','JPG','JPEG','GIF'); 
        if(in_array($fileType, $allowTypes)){ 
            
			if(!is_dir($uploadPath)){
			mkdir($uploadPath, 0755);
				}
	 
	   $imageUploadPath1 .= "documents/profiles/".$time."-".$userid."-".$documenttype.".".$fileType;
	   file_put_contents($imageUploadPath1,$filepath);
		
$statusMsg =$imageUploadPath1;		
        }else{ 
            $statusMsg = 'Invalid image file'; 
        }
		return $statusMsg;
} 

echo json_encode($response);
$pdoread= null;
$pdo4= null;
?>