<?PHP
$servername = "3.6.65.78";
$username = "hims";
$password = "QI7TQ2Ga1ZoVZ0-[";
$database = "hims";
$baseurl = "http://alb-app-1655237078.ap-south-1.elb.amazonaws.com/mobile-api/employee/";
try{
    $pdo = new PDO("mysql:host=" . $servername . ";dbname=" . $database.";charset=utf8", $username, $password);
  
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
} catch(PDOException $e){
    die("ERROR: Could not connect. " . $e->getMessage());
}
?>