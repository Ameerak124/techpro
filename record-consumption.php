<?php
include "db-pdo.php";
$empname = $_SESSION['username'];
$accesskey = $_SESSION['accesskey'];
$emp_query = "SELECT  `userid`, `password`, `username`, `emailid`, `mobile`, `role`, `desgination`, `department`, `accesskey` FROM `user_logins` WHERE `accesskey` = :accesskey";
$emp_sbmt = $pdo->prepare($emp_query);
$emp_sbmt->bindParam(":accesskey", $accesskey, PDO::PARAM_STR);
$emp_sbmt->execute();
if (!$emp_sbmt->rowCount() > 0) {
     echo '<script type="text/javascript">alert("Access Denied!")</script>';
     echo '<script type="text/javascript">window.location.href="logout.php"</script>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
     <title>Indent List</title>
     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="description" content="#">
     <meta name="keywords" content="Admin , Responsive, Landing, Bootstrap, App, Template, Mobile, iOS, Android, apple, creative app">
     <meta name="author" content="#">
     <!-- Favicon icon -->
     <link rel="icon" href="libraries\assets\images\favicon.ico" type="image/x-icon">
     <!-- Google font-->
     <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,800" rel="stylesheet">
     <!-- Required Fremwork -->
     <link rel="stylesheet" type="text/css" href="libraries\bower_components\bootstrap\css\bootstrap.min.css">
     <!-- themify-icons line icon -->
     <link rel="stylesheet" type="text/css" href="libraries\assets\icon\themify-icons\themify-icons.css">
     <!-- ico font -->
     <link rel="stylesheet" type="text/css" href="libraries\assets\icon\icofont\css\icofont.css">
     <!-- feather Awesome -->
     <link rel="stylesheet" type="text/css" href="libraries\assets\icon\feather\css\feather.css">
     <!-- Font Awesome -->
     <link rel="stylesheet" type="text/css" href="libraries\assets\icon\font-awesome\css\font-awesome.min.css">
     <!-- Data Table Css -->
     <link rel="stylesheet" type="text/css" href="libraries\bower_components\datatables.net-bs4\css\dataTables.bootstrap4.min.css">
     <link rel="stylesheet" type="text/css" href="libraries\assets\pages\data-table\css\buttons.dataTables.min.css">
     <link rel="stylesheet" type="text/css" href="libraries\bower_components\datatables.net-responsive-bs4\css\responsive.bootstrap4.min.css">
     <!-- Style.css -->
     <link rel="stylesheet" type="text/css" href="libraries\assets\css\style.css">
     <link rel="stylesheet" type="text/css" href="libraries\assets\css\jquery.mCustomScrollbar.css">

     <!-- autocomplete css -->
     <link rel="stylesheet" href="//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css">

     <style>
          .container {
               margin-top: 100px
          }

          .modal-open .modal {
               overflow-x: hidden;
               overflow-y: auto;
               background-size: cover
          }

          .modal-content {
               background-color: #fff;
               color: black;
               padding: 13px
          }

          .container-chk {
               display: block;
               position: relative;
               padding-left: 22px;
               margin-bottom: 12px;
               cursor: pointer;
               font-size: 14px;
               -webkit-user-select: none;
               -moz-user-select: none;
               -ms-user-select: none;
               user-select: none;
               height: 52px
          }

          .set {
               width: 100%;
               height: 67px;
               border-radius: 4px;
               background: #252222;
               padding-top: 16px;
               padding-left: 16px;
               margin-bottom: 10px
          }

          .container-chk input {
               position: absolute;
               opacity: 0;
               cursor: pointer;
               height: 0;
               width: 0
          }

          .checkmark {
               position: absolute;
               top: 0;
               left: 0;
               height: 17px;
               width: 17px;
               border-radius: 3px;
               background-color: #212121;
               border: 1px solid
          }

          .container-chk:hover input~.checkmark {
               background-color: white
          }

          .container-chk input:checked~.checkmark {
               background-color: white
          }

          .checkmark:after {
               content: "";
               position: absolute;
               display: none
          }

          .container-chk input:checked~.checkmark:after {
               display: block
          }

          .container-chk .checkmark:after {
               left: 5px;
               top: -1px;
               width: 5px;
               height: 13px;
               border: solid #212121;
               border-width: 0 1px 1px 0;
               -webkit-transform: rotate(45deg);
               -ms-transform: rotate(45deg);
               transform: rotate(45deg)
          }

          .small,
          small {
               font-size: 80%;
               font-weight: 400;
               position: relative;
               bottom: 8px
          }

          .txt {
               position: relative;
               bottom: 4px;
               left: 2px
          }

          .cancel {
               border: 1px solid #252222;
               background: #151414;
               color: white;
               width: 40%
          }

          .btn.focus,
          .btn:focus {
               outline: 0;
               box-shadow: none !important
          }

          .close.focus,
          .close:focus {
               outline: 0;
               box-shadow: none !important
          }

          .create {
               border: 0.5px solid #ccc;
               background: #151414;
               color: white;
               width: 40%
          }

          .close {
               color: white;
               font-weight: 100
          }

          .modal-footer {
               justify-content: space-between
          }

          .ui-autocomplete {
               max-height: 500px;
               overflow-y: auto;
               /* prevent horizontal scrollbar */
               overflow-x: hidden;
               /* add padding to account for vertical scrollbar */
               padding-right: 20px;
          }

          strong {
               color: black !important;
          }


          #itemtable thead tr th {
               white-space: normal;
          }

          #itemtable tbody tr td {
               white-space: normal;
          }
     </style>
</head>

<body>
     <!-- Pre-loader start -->
     <div class="theme-loader">
          <div class="ball-scale">
               <div class='contain'>
                    <div class="ring">
                         <div class="frame"></div>
                    </div>
                    <div class="ring">
                         <div class="frame"></div>
                    </div>
                    <div class="ring">
                         <div class="frame"></div>
                    </div>
                    <div class="ring">
                         <div class="frame"></div>
                    </div>
                    <div class="ring">
                         <div class="frame"></div>
                    </div>
                    <div class="ring">
                         <div class="frame"></div>
                    </div>
                    <div class="ring">
                         <div class="frame"></div>
                    </div>
                    <div class="ring">
                         <div class="frame"></div>
                    </div>
                    <div class="ring">
                         <div class="frame"></div>
                    </div>
                    <div class="ring">
                         <div class="frame"></div>
                    </div>
               </div>
          </div>
     </div>
     <!-- Pre-loader end -->
     <div id="pcoded" class="pcoded">
          <div class="pcoded-overlay-box"></div>
          <div class="pcoded-container navbar-wrapper">
               <?php include "top-navigation-emp.php"; ?>
               <div class="pcoded-main-container">
                    <div class="pcoded-wrapper">
                         <?php include "side-navigation-emp.php"; ?>
                         <div class="pcoded-content">
                              <div class="pcoded-inner-content">
                                   <!-- accesskey -->
                                   <input type="hidden" class="form-control" id="accesskey" name="accesskey" value="<?php echo $accesskey ?>" />
                                   <!-- accesskey -->
                                   <!-- Main-body start -->
                                   <div class="main-body">
                                <div class="page-wrapper">

                                    <div class="page-body">
                                        <div class="row">

                                            <div class="col-sm-12">
                                                <div class="card card-border-warning">
                                                    <div class="card-header" style="margin: 0px;padding:0px;">
                                                        <form method="POST" id="addcharges">
                                                            <div class="card-body pt-2">
                                                                <div class="row" id="pDetails">
                                                                    <div class="col-lg-3 mt-1">
                                                                        <span class="">User Name: <strong id="username">Rajesh</strong></span>
                                                                    </div>
                                                                    <div class="mt-1 col-lg-3">
                                                                        <span class="">Department Name: <strong id="department">Development</strong></span>
                                                                    </div>
                                                                    <div class="mt-1 col-lg-3">
                                                                        <span class="">Stock Point : <strong id="stcokpoint">GW -1 </strong></span>
                                                                    </div>
                                                                    <div class="mt-1 col-lg-3">
                                                                        <span class="">Consumption ID : <strong id="dynamicid"></strong></span>
                                                                    </div>
                                                                    <div class="mt-1 col-lg-3 d-none">
                                                                        <span class="">Consumption ID : <strong id="consumptionid"></strong></span>
                                                                    </div>
                                                                    
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12">
                                                <div class="card">
                                                    <div class="card-block">
                                                        <div class="row">
                                                            <div class="col-lg-7">
                                                                <input type="text" class="form-control" placeholder="Search by product name..." style="font-size:16px;">
                                                            </div>
                                                            <div class="col-lg-3">
                                                                <input type="text" class="form-control" placeholder="Enter Consumed Quantity" style="font-size:16px;">
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <button class="btn btn-mat btn-primary">Add Product</button>
                                                            </div>
                                                        </div>
                                                        <div class="dt-responsive table-responsive mt-3">
                                                            <table id="base-style" class="table table-striped table-bordered nowrap">
                                                                <thead>
                                                                    <tr>
                                                                        <th>S.No</th>
                                                                        <th>Item Code</th>
                                                                        <th>Item Name</th>
                                                                        <th>Store</th>
                                                                        <th>Consumed Qty</th>
                                                                        <th>UOM</th>
                                                                        <th>Value</th>
                                                                        <th>Actions</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td>1</td>
                                                                        <td>MHA01234</td>
                                                                        <td>Dolo 650 MG</td>
                                                                        <td>Central Pharmacy</td>
                                                                        <td>10</td>
                                                                        <td>Tablet</td>
                                                                        <td>&#8377; 1,200/-</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>2</td>
                                                                        <td>MHA01324</td>
                                                                        <td>Toa Sanitizer 500ml</td>
                                                                        <td>Central Pharmacy</td>
                                                                        <td>100</td>
                                                                        <td>Bottle</td>
                                                                        <td>&#8377; 10,000/-</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>3</td>
                                                                        <td>MHA03214</td>
                                                                        <td>Cotton rolls</td>
                                                                        <td>Central Pharmacy</td>
                                                                        <td>50</td>
                                                                        <td>Pack</td>
                                                                        <td>&#8377; 1,000/-</td>
                                                                    </tr>
                                                                </tbody>
                                                                <tfoot>
                                                                    <tr>
                                                                        <th>S.No</th>
                                                                        <th>Item Code</th>
                                                                        <th>Item Name</th>
                                                                        <th>Store</th>
                                                                        <th>Consumed Qty</th>
                                                                        <th>UOM</th>
                                                                        <th>Value</th>
                                                                        <th>Actions</th>
                                                                    </tr>
                                                                </tfoot>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                   <!-- Main-body end -->
                              </div>
                         </div>
                    </div>
               </div>
          </div>
     </div>




     <!-- Warning Section Ends -->
     <!-- Required Jquery -->
     <script type="text/javascript" src="libraries\bower_components\jquery\js\jquery.min.js"></script>
     <script type="text/javascript" src="libraries\bower_components\jquery-ui\js\jquery-ui.min.js"></script>
     <script type="text/javascript" src="libraries\bower_components\popper.js\js\popper.min.js"></script>
     <script type="text/javascript" src="libraries\bower_components\bootstrap\js\bootstrap.min.js"></script>
     <!-- jquery slimscroll js -->
     <script type="text/javascript" src="libraries\bower_components\jquery-slimscroll\js\jquery.slimscroll.js"></script>
     <!-- modernizr js -->
     <script type="text/javascript" src="libraries\bower_components\modernizr\js\modernizr.js"></script>
     <script type="text/javascript" src="libraries\bower_components\modernizr\js\css-scrollbars.js"></script>

     <!-- data-table js -->
     <script src="libraries\bower_components\datatables.net\js\jquery.dataTables.min.js"></script>
     <script src="libraries\bower_components\datatables.net-buttons\js\dataTables.buttons.min.js"></script>
     <script src="libraries\assets\pages\data-table\js\jszip.min.js"></script>
     <script src="libraries\assets\pages\data-table\js\pdfmake.min.js"></script>
     <script src="libraries\assets\pages\data-table\js\vfs_fonts.js"></script>
     <script src="libraries\bower_components\datatables.net-buttons\js\buttons.print.min.js"></script>
     <script src="libraries\bower_components\datatables.net-buttons\js\buttons.html5.min.js"></script>
     <script src="libraries\bower_components\datatables.net-bs4\js\dataTables.bootstrap4.min.js"></script>
     <script src="libraries\bower_components\datatables.net-responsive\js\dataTables.responsive.min.js"></script>
     <script src="libraries\bower_components\datatables.net-responsive-bs4\js\responsive.bootstrap4.min.js"></script>
     <!-- i18next.min.js -->
     <script type="text/javascript" src="libraries\bower_components\i18next\js\i18next.min.js"></script>
     <script type="text/javascript" src="libraries\bower_components\i18next-xhr-backend\js\i18nextXHRBackend.min.js"></script>
     <script type="text/javascript" src="libraries\bower_components\i18next-browser-languagedetector\js\i18nextBrowserLanguageDetector.min.js"></script>
     <script type="text/javascript" src="libraries\bower_components\jquery-i18next\js\jquery-i18next.min.js"></script>
     <!-- Custom js -->
     <script src="libraries\assets\pages\data-table\js\data-table-custom.js"></script>

     <script src="libraries\assets\js\pcoded.min.js"></script>
     <script src="libraries\assets\js\vartical-layout.min.js"></script>
     <script src="libraries\assets\js\jquery.mCustomScrollbar.concat.min.js"></script>
     <script type="text/javascript" src="libraries\assets\js\script.js"></script>
     <!-- autocomplete ui js end -->
     <script src="https://code.jquery.com/ui/1.13.0/jquery-ui.js"></script>


     <!-- script start  -->
     <script>
          const accesskey = document.getElementById("accesskey").value;
          $(document).ready(function(){
               $.ajax({
                         url:"mobile-api/get-userdetails.php",
                         data:JSON.stringify({
                              "accesskey":accesskey
                         }),
                         type:"POST",
                         success:(response)=>{
                              if(response.error === false){
                                   $("#username").html(response.username);
                                   $("#stcokpoint").html(response.stock_point);
                                   $("#department").html(response.department);
                                   if(response.stock_point != "NA"){
                                        $.ajax({
                                             url:"mobile-api/get-consumptionitems.php",
                                             data:JSON.stringify({
                                                       "accesskey":accesskey
                                                  }),
                                             type:"POST",
                                             success:function(response){
                                                  if(response.error === false){
                                                       $("dynamicid").html(response.consumptionid);
                                                  }
                                             }     

                                        })
                                   }
                              }
                              else{
                                   alert(response.message);
                              }
                         }
                    })

          })


     </script>
     <!-- script end  -->
</body>

</html>