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
$from_address = $data->from_address;
$dc_invoice_no = $data->dc_invoice_no;
$dc_invoice_date = date('Y-m-d', strtotime($data->dc_invoice_date));
$description = $data->description;
$qty = $data->qty;
$vehicle_no = $data->vehicle_no;
$brought_by = $data->brought_by;
$remarks = $data->remarks;
$signatureattach = $data->signatureattach;
$signatureattachfilename=$data->signatureattachfilename;
$decodedsignatureattachimage=base64_decode($signatureattach);
$response = array();
try{

 if(!empty($accesskey)){
$accesscheck ="SELECT `userid`,`branch`,`cost_center`,username,role FROM `user_logins` WHERE `mobile_accesskey` = :accesskey AND `status` = 'Active'";
	$stmt = $pdoread->prepare($accesscheck);
	$stmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
	$stmt->execute();
	if($stmt->rowCount() > 0){
	$row = $stmt->fetch(PDO::FETCH_ASSOC);

      $result2 = $pdo1 -> prepare("SELECT Concat('INW',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m'),LPAD((SUBSTRING_INDEX(COALESCE(MAX(`id`),'INW23090000'),Concat('INW',DATE_FORMAT(CURRENT_DATE,'%y'),DATE_FORMAT(CURRENT_DATE,'%m')),-1)+1),'5','0')) AS id FROM `inward` where id like concat('%INW',DATE_FORMAT(CURRENT_DATE,'%y'),'%') LIMIT 1");
					$result2->execute();
                        $data=$result2->fetch(PDO::FETCH_ASSOC);
					$inward_id=$data['id'];

$time=date("Y-m-d-H-i-s");		
				
	if(!empty($signatureattachfilename)){ 
        
		 $documenttype = 'signatureattach';
		$filename = $signatureattachfilename;
		 $fileType = pathinfo($signatureattachfilename, PATHINFO_EXTENSION); 
		
		$fileName = basename($signatureattachfilename);
		$filesize = decodedsignatureattachimage["size"];
		$filepath = decodedsignatureattachimage['tmp_name'];
		if($fileType=='PDF' or $fileType=='pdf'){
			$signatureattach = pdfupload($filename,$filesize,$inward_id,$time,$documenttype,$decodedsignatureattachimage);
		 } else {
			
				$signatureattach = imageupload($filename,$inward_id,$time,$documenttype,$decodedsignatureattachimage);
			 
		 }	
		
		}else{ 
        $signatureattach = 'Please select an image file to upload.'; 
    } 

     $result = $pdo1 -> prepare("INSERT INTO `inward`( `id`, `date_time`, `from_address`, `dc_invoice_no`, `dc_invoice_date`, `description`, `qty`, `vehicle_no`, `brought_by`, `signature`, `remarks`, `status`, `created_by`, `created_on`) VALUES (:id,CURRENT_TIMESTAMP,:from_address,:dc_invoice_no,:dc_invoice_date,:description,:qty,:vehicle_no,:brought_by,:signatureattach,:remarks,'Received',:userid,CURRENT_TIMESTAMP)");
	$result->bindParam(':id', $inward_id, PDO::PARAM_STR);
	$result->bindParam(':from_address', $from_address, PDO::PARAM_STR);
	$result->bindParam(':dc_invoice_no', $dc_invoice_no, PDO::PARAM_STR);
	$result->bindParam(':dc_invoice_date', $dc_invoice_date, PDO::PARAM_STR);
	$result->bindParam(':description', $description, PDO::PARAM_STR);
	$result->bindParam(':qty', $qty, PDO::PARAM_STR);
	$result->bindParam(':vehicle_no', $vehicle_no, PDO::PARAM_STR);
	$result->bindParam(':brought_by', $brought_by, PDO::PARAM_STR);
	$result->bindParam(':signatureattach', $signatureattach, PDO::PARAM_STR);
	$result->bindParam(':remarks', $remarks, PDO::PARAM_STR);
    $result->bindParam(':userid', $row['userid'], PDO::PARAM_STR);
	$result-> execute();

     $result1 = $pdo1 -> prepare("INSERT INTO `inward_log`(`id`, `dc_invoice_no`, `dc_invoice_date`, `vehicle_no`, `created_on`,`brought_by`,`status`) VALUES (:id,:dc_invoice_no,:dc_invoice_date,:vehicle_no,CURRENT_TIMESTAMP,:brought_by,'Received')");
	$result1->bindParam(':id', $inward_id, PDO::PARAM_STR);
	$result1->bindParam(':dc_invoice_no', $dc_invoice_no, PDO::PARAM_STR);
	$result1->bindParam(':dc_invoice_date', $dc_invoice_date, PDO::PARAM_STR);
	$result1->bindParam(':vehicle_no', $vehicle_no, PDO::PARAM_STR);
	$result1->bindParam(':brought_by', $brought_by, PDO::PARAM_STR);
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
function pdfupload($filename,$filesize,$inward_id,$time,$documenttype,$filepath){
	$fileType = pathinfo($filename, PATHINFO_EXTENSION); 
if ($filesize < 500000)//500000 = 500kb
    {
		
       //$location1 = "documents/images/".$dependent_name;
       $location = "documents/images/".$inward_id;
	 
    
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
function imageupload($filename,$inward_id,$time,$documenttype,$filepath){
	
	$fileType = pathinfo($filename, PATHINFO_EXTENSION); 

			$allowTypes = array('jpg','png','jpeg','gif','PNG','JPG','JPEG','GIF'); 
        if(in_array($fileType, $allowTypes)){ 
            
			if(!is_dir($uploadPath)){
			mkdir($uploadPath, 0755);
				}
	 
	   $imageUploadPath1 .= "documents/images/".$time."-".$inward_id."-".$documenttype.".".$fileType;
	   file_put_contents($imageUploadPath1,$filepath);
		
$statusMsg =$imageUploadPath1;		
        }else{ 
            $statusMsg = 'Invalid image file'; 
        }
		return $statusMsg;
} 
echo json_encode($response,true);
unset($pdoread);
unset($pd1);

?>