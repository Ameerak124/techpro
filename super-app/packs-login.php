<?php
$username=$_GET['Username'];
$Password=$_GET['Password'];
$patientID=$_GET['patientID'];

?>
<!DOCTYPE html> 
<html> 
<style>
.button {
  background-color: #04AA6D; /* Green */
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;
}


.vertical-center {
	
   margin: 0;
  position: absolute;
  top: 50%;
  left: 50%;
  -ms-transform: translate(-50%, -50%);
  transform: translate(-50%, -50%);
}
</style>
<head> 
</head> 
<body>


  <div class="vertical-center"> 
<button type="button" onclick="myFunction()" class="button">View the report</button>
  </div>


</body> 
	<script> 
		
		function myFunction() {
   
       
        var Form = document.createElement("form");
        // Form.action = 'http://14.98.213.178:5353/Launch_Viewer.asp';
        Form.action = 'http://10.74.0.50/PACSLogin';
        Form.method = "post";
        Form.target = "_blank";
        var username = document.createElement("input");
        username.value = "DemoUser";
        username.name = "LastLoginUser";
        username.type = "text";
        var password = document.createElement("input");
        password.value = "12345";
        password.name = "LastLoginSessionID";
        password.type = "text";
        var patientID = $('#Cache_Server').find('option:selected').text();
        patientID.value = "Begumpeth";
        patientID.name = "Cache_Server";
        patientID.type = "text";
        // var submit = document.createElement("button");
        // submit.type = "submit";
        Form.appendChild(username)
        Form.appendChild(password)
        Form.appendChild(patientID)
        // Form.appendChild(submit)
        var formToSubmit = document.body.appendChild(Form);
        formToSubmit.style.display = 'none';
        Form.submit();
    }
		
	</script> 

</html>
