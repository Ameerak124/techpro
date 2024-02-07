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
     <title>Return Items</title>
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
               max-height: 250px;
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

                                   <!-- access key -->
                                   <input type="hidden" name="accesskey" id="accesskey" value="<?php echo $accesskey ?>" />

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
                                                                                          <span class="">User Name: <strong id="username"></strong></span>
                                                                                     </div>
                                                                                     <div class="mt-1 col-lg-3">
                                                                                          <span class="">Department Name: <strong id="department"></strong></span>
                                                                                     </div>
                                                                                     <div class="mt-1 col-lg-3">
                                                                                          <span class="">Stock Point : <strong id="stcokpoint"></strong></span>
                                                                                     </div>
                                                                                     <div class="mt-1 col-lg-3">
                                                                                          <span class="">Return ID : <strong id="dynamicid"></strong></span>
                                                                                     </div>
                                                                                     <div class="mt-1 col-lg-3 d-none">
                                                                                          <span class="">Return ID : <strong id="returnid"></strong></span>
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
                                                                      <form id="itemform" method="POST" action="">
                                                                           <div class="row">
                                                                                <div class="col-lg-2 d-none">
                                                                                     <div class="checkbox-fade fade-in-danger ">
                                                                                          <label style="padding: 0.5rem 0.75rem;">
                                                                                               <input type="checkbox" name="priority" id="priority" value="emergency">
                                                                                               <span class="cr">
                                                                                                    <i class="cr-icon icofont icofont-ui-check txt-danger"></i>
                                                                                               </span>
                                                                                               <span style="font-size:16px;"> Is Urgent?</span>
                                                                                          </label>
                                                                                     </div>
                                                                                </div>
                                                                                <div class="col-lg-7">
                                                                                     <input type="text" class="form-control" placeholder="Search by product name..." style="font-size:16px;" id="itemname" name="itemname">
                                                                                </div>
                                                                                <div class="col-lg-3">
                                                                                     <input type="number" class="form-control" placeholder="Enter Required Quantity" name="qty" id="itemqty" style="font-size:16px;" step="0.00001">
                                                                                </div>
                                                                                <input type="hidden" name="sno" id="itemsno" />
                                                                                <input type="hidden" name="itemcode" id="itemcode" />
                                                                                <input type="hidden" name="censno" id="censno" />
                                                                                <input type="hidden" name="batchno" id="batchno" />
                                                                                <div class="col-lg-2">
                                                                                     <button class="btn btn-mat btn-primary" type="submit" id="submit">Add Product</button>
                                                                                </div>
                                                                           </div>
                                                                      </form>
                                                                      <div class="dt-responsive table-responsive mt-3">
                                                                           <table id="base-style" class="table table-striped table-bordered nowrap">
                                                                                <thead>
                                                                                     <tr>
                                                                                          <th>S.No</th>
                                                                                          <th>Return#</th>
                                                                                          <th>Item Code</th>
                                                                                          <th>Item Name</th>
                                                                                          <th>Store</th>
                                                                                          <th>Return Qty</th>
                                                                                          <th>Batch No</th>
                                                                                          <th>Actions</th>
                                                                                     </tr>
                                                                                </thead>
                                                                                <tbody>
                                                                                </tbody>
                                                                                <tfoot>
                                                                                     <tr>
                                                                                          <th>S.No</th>
                                                                                          <th>Return#</th>
                                                                                          <th>Item Code</th>
                                                                                          <th>Item Name</th>
                                                                                          <th>Store</th>
                                                                                          <th>Return Qty</th>
                                                                                          <th>Batch No</th>
                                                                                          <th>Actions</th>
                                                                                     </tr>
                                                                                </tfoot>
                                                                           </table>
                                                                      </div>
                                                                      <div class="row">
                                                                           <div class="col-lg-12 text-right">
                                                                                <button class="btn btn-mat btn-primary" type="submit" id="saveindent" name="saveindent">Save</button>
                                                                           </div>
                                                                      </div>
                                                                 </div>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                                   <!-- Page body end -->
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
          const accesskey = document.getElementById('accesskey').value;

          /* get user details start */
          $(document).ready(function() {
               document.getElementById("itemname").focus();
               $.ajax({
                    url: "mobile-api/get-userdetails.php",
                    data: JSON.stringify({
                         "accesskey": accesskey
                    }),
                    type: "POST",
                    success: (response) => {
                         if (response.error === false) {
                              console.log(response.empdetails.username);
                              $("#username").html(response.empdetails.username);
                              $("#stcokpoint").html(response.empdetails.stock_point);
                              $("#department").html(response.empdetails.department);
                              if (response.empdetails.stock_point != "NA") {
                                   $.ajax({
                                        url: "mobile-api/get-returnid.php",
                                        data: JSON.stringify({
                                             "accesskey": accesskey
                                        }),
                                        type: "POST",
                                        success:(response) => {
                                             if (response.error === false) {
                                                  $("#dynamicid").html(response.returnid);
                                             }
                                        }
                                   })
                              }
                         } else {
                              alert(response.message);
                         }
                    }
               })

          })
          /* get user details start */


          $(document).ready(function() {
               $("#itemname").autocomplete({
                    
                         source: function(request, response) {
                              var stockpoint = $("#stcokpoint").html();
                              var term = request.term;
                              var dat = [];
                              $.ajax({
                                   type: "POST",
                                   url: "mobile-api/get-stock-itemlist.php",
                                   data: JSON.stringify({
                                        "searchitem": term,
                                        "accesskey": accesskey,
                                        "stockpoint":"stockpoint2"
                                   }),
                                   success: function(data) {
                                        var dets = [];
                                        if (data.error === false) {
                                             $.each(data.stockitemlist, function(index, value) {
                                                  /* console.log(data.stockitemlist); */

                                                  let itemcode = data.stockitemlist[index].itemcode;
                                                  let itemname = data.stockitemlist[index].itemname;
                                                  let qty = data.stockitemlist[index].qty;
                                                  let itemsno = data.stockitemlist[index].itemsno;
                                                  let censno = data.stockitemlist[index].censno;
                                                  let batch_no = data.stockitemlist[index].batch_no;
                                                  dets.push({
                                                       label: itemname,
                                                       value: itemname,
                                                       qty,
                                                       itemsno,
                                                       itemcode,
                                                       censno,
                                                       itemname,
                                                       batch_no
                                                  })
                                             });
                                             response(dets.slice(0, 10));
                                        }

                                   },
                              })
                         },
                         minLength: 2,
                         select: function(event, ui) {
                              $("#itemname").val(ui.item.itemname);
                              $("#itemqty").val(ui.item.qty);
                              $("#itemqty").attr({
                                   "max": ui.item.qty
                              })
                              $("#itemsno").val(ui.item.itemsno);
                              $("#itemcode").val(ui.item.itemcode);
                              $("#censno").val(ui.item.censno);
                         }
                    })

                    .autocomplete("instance")._renderItem = function(ul, item) {
                         return $("<li>")
                              .append("<div><b>" + item.itemname + "</b><br><p  style='font-size:12px'><b>ItemCode :<b>" + item.itemcode + "</b>   Stock : <b>" + item.qty + "</b>  Batch : <b>" + item.batch_no + "<p></div>")
                              .appendTo(ul);
                    };
          })


          /* submit details start */
          $(document).ready(function(){
               $("#itemform").submit(function(event){
               event.preventDefault();
               var itemdata = $("#itemform").serializeJSON()
               var department = $("#department").html();
               var returnid = $("#returnid").html();
               console.log(itemdata);
               itemdata.accesskey = accesskey;
               itemdata.return_id = returnid;
               itemdata.department = department;
               /* itemdata.push({
                    "name":"accesskey",
                    "value":accesskey
               }) */
               $.ajax({
                         data:JSON.stringify(
                              itemdata
                         ),
                         url:"mobile-api/gen-return.php",
                         type:"POST",
                         success:function(response){
                              console.log(response);
                              if(response.error === false){
                                   alert(response.message);
                                   document.getElementById("itemform").reset();
                                   $("#returnid").html(response?.return_id);
                                   $("#dynamicid").html(response?.return_id);
                                   var data = response?.returnitemlist;
                                   $("#base-style").DataTable().clear().destroy();
                                   $("#base-style").DataTable({
                         data: data,
                         pageLength: 10,
                         deferRender: true,
                         searching: true,
                         columns: [
                              {
                                   "data": "sno"
                              },
                              {
                                   "data": "return_id"
                              },
                              {
                                   "data": "item_code"
                              },
                              {
                                   "data": "item_name"
                              },
                              {
                                   "data": "stock_point"
                              },
                             
                              {
                                   "data": "return_qty"
                              },
                              {
                                   "data": "batch_no"
                              },
                              {
                                   "mData": null,
                                   "bSortable": false,
                                   "mRender": function(data, type, full) {
                                             return `<div class="text-center"><td><a class="btn btn-outlined  btn-outline-danger btn-sm deleteitem" data-delid="${data.sno}"><i class="fa fa-trash" ></i></a></td></div>`;
                                   }
                              },
                         ]
                    })

                         $("#itemname").focus();
                              }
                              else
                              {
                                   alert(response.message);
                                   $("#itemname").focus();
                              }
                         }
                    })
                    })

                window.onbeforeunload = function ()
                    {
                        return "Are You sure!";
                    }; 
          })
          /* submit details end */

          

          /* save indet to route to indent list start */
          $(document).ready(function() {
               $("#saveindent").click(function(event) {
                    event.preventDefault();
                    var indentno = $("#indentno").html();
                    if (indentno != "") {
                         $.ajax({
                              data: JSON.stringify({
                                   "accesskey": accesskey,
                                   "indentno": indentno
                              }),
                              url: "api/submit-indent.php",
                              type: "POST",
                              success: (response) => {
                                   if (response.error === false) {
                                        alert(response?.message);
                                        window.location.href = "indent-list.php"
                                   } else {
                                        alert(response.message);
                                   }

                              }
                         })
                    } else {
                         alert("Invalid Indent Number");
                    }

               })
          })
          /* save indet to route to indent list end */
     </script>
     <!-- script end  -->

     <!-- serialize json script start -->
     <script>
     
     (function (factory) {
         /* global define, require, module */
         if (typeof define === "function" && define.amd) { // AMD. Register as an anonymous module.
             define(["jquery"], factory);
         } else if (typeof exports === "object") { // Node/CommonJS
             var jQuery = require("jquery");
             module.exports = factory(jQuery);
         } else { // Browser globals (zepto supported)
             factory(window.jQuery || window.Zepto || window.$); // Zepto supported on browsers as well
         }
     
     }(function ($) {
         "use strict";
     
         var rCRLF = /\r?\n/g;
         var rsubmitterTypes = /^(?:submit|button|image|reset|file)$/i;
         var rsubmittable = /^(?:input|select|textarea|keygen)/i;
         var rcheckableType = /^(?:checkbox|radio)$/i;
     
         $.fn.serializeJSON = function (options) {
             var f = $.serializeJSON;
             var $form = this; // NOTE: the set of matched elements is most likely a form, but it could also be a group of inputs
             var opts = f.setupOpts(options); // validate options and apply defaults
             var typeFunctions = $.extend({}, opts.defaultTypes, opts.customTypes);
     
             // Make a list with {name, value, el} for each input element
             var serializedArray = f.serializeArray($form, opts);
     
             // Convert the serializedArray into a serializedObject with nested keys
             var serializedObject = {};
             $.each(serializedArray, function (_i, obj) {
     
                 var nameSansType = obj.name;
                 var type = $(obj.el).attr("data-value-type");
     
                 if (!type && !opts.disableColonTypes) { // try getting the type from the input name
                     var p = f.splitType(obj.name); // "foo:string" => ["foo", "string"]
                     nameSansType = p[0];
                     type = p[1];
                 }
                 if (type === "skip") {
                     return; // ignore fields with type skip
                 }
                 if (!type) {
                     type = opts.defaultType; // "string" by default
                 }
     
                 var typedValue = f.applyTypeFunc(obj.name, obj.value, type, obj.el, typeFunctions); // Parse type as string, number, etc.
     
                 if (!typedValue && f.shouldSkipFalsy(obj.name, nameSansType, type, obj.el, opts)) {
                     return; // ignore falsy inputs if specified in the options
                 }
     
                 var keys = f.splitInputNameIntoKeysArray(nameSansType);
                 f.deepSet(serializedObject, keys, typedValue, opts);
             });
             return serializedObject;
         };
     
         // Use $.serializeJSON as namespace for the auxiliar functions
         // and to define defaults
         $.serializeJSON = {
             defaultOptions: {}, // reassign to override option defaults for all serializeJSON calls
     
             defaultBaseOptions: { // do not modify, use defaultOptions instead
                 checkboxUncheckedValue: undefined, // to include that value for unchecked checkboxes (instead of ignoring them)
                 useIntKeysAsArrayIndex: false, // name="foo[2]" value="v" => {foo: [null, null, "v"]}, instead of {foo: ["2": "v"]}
     
                 skipFalsyValuesForTypes: [], // skip serialization of falsy values for listed value types
                 skipFalsyValuesForFields: [], // skip serialization of falsy values for listed field names
     
                 disableColonTypes: false, // do not interpret ":type" suffix as a type
                 customTypes: {}, // extends defaultTypes
                 defaultTypes: {
                     "string":  function(str) { return String(str); },
                     "number":  function(str) { return Number(str); },
                     "boolean": function(str) { var falses = ["false", "null", "undefined", "", "0"]; return falses.indexOf(str) === -1; },
                     "null":    function(str) { var falses = ["false", "null", "undefined", "", "0"]; return falses.indexOf(str) === -1 ? str : null; },
                     "array":   function(str) { return JSON.parse(str); },
                     "object":  function(str) { return JSON.parse(str); },
                     "skip":    null // skip is a special type used to ignore fields
                 },
                 defaultType: "string",
             },
     
             // Validate and set defaults
             setupOpts: function(options) {
                 if (options == null) options = {};
                 var f = $.serializeJSON;
     
                 // Validate
                 var validOpts = [
                     "checkboxUncheckedValue",
                     "useIntKeysAsArrayIndex",
     
                     "skipFalsyValuesForTypes",
                     "skipFalsyValuesForFields",
     
                     "disableColonTypes",
                     "customTypes",
                     "defaultTypes",
                     "defaultType"
                 ];
                 for (var opt in options) {
                     if (validOpts.indexOf(opt) === -1) {
                         throw new  Error("serializeJSON ERROR: invalid option '" + opt + "'. Please use one of " + validOpts.join(", "));
                     }
                 }
     
                 // Helper to get options or defaults
                 return $.extend({}, f.defaultBaseOptions, f.defaultOptions, options);
             },
     
             // Just like jQuery's serializeArray method, returns an array of objects with name and value.
             // but also includes the dom element (el) and is handles unchecked checkboxes if the option or data attribute are provided.
             serializeArray: function($form, opts) {
                 if (opts == null) { opts = {}; }
                 var f = $.serializeJSON;
     
                 return $form.map(function() {
                     var elements = $.prop(this, "elements"); // handle propHook "elements" to filter or add form elements
                     return elements ? $.makeArray(elements) : this;
     
                 }).filter(function() {
                     var $el = $(this);
                     var type = this.type;
     
                     // Filter with the standard W3C rules for successful controls: http://www.w3.org/TR/html401/interact/forms.html#h-17.13.2
                     return this.name && // must contain a name attribute
                         !$el.is(":disabled") && // must not be disable (use .is(":disabled") so that fieldset[disabled] works)
                         rsubmittable.test(this.nodeName) && !rsubmitterTypes.test(type) && // only serialize submittable fields (and not buttons)
                         (this.checked || !rcheckableType.test(type) || f.getCheckboxUncheckedValue($el, opts) != null); // skip unchecked checkboxes (unless using opts)
     
                 }).map(function(_i, el) {
                     var $el = $(this);
                     var val = $el.val();
                     var type = this.type; // "input", "select", "textarea", "checkbox", etc.
     
                     if (val == null) {
                         return null;
                     }
     
                     if (rcheckableType.test(type) && !this.checked) {
                         val = f.getCheckboxUncheckedValue($el, opts);
                     }
     
                     if (isArray(val)) {
                         return $.map(val, function(val) {
                             return { name: el.name, value: val.replace(rCRLF, "\r\n"), el: el };
                         } );
                     }
     
                     return { name: el.name, value: val.replace(rCRLF, "\r\n"), el: el };
     
                 }).get();
             },
     
             getCheckboxUncheckedValue: function($el, opts) {
                 var val = $el.attr("data-unchecked-value");
                 if (val == null) {
                     val = opts.checkboxUncheckedValue;
                 }
                 return val;
             },
     
             // Parse value with type function
             applyTypeFunc: function(name, strVal, type, el, typeFunctions) {
                 var typeFunc = typeFunctions[type];
                 if (!typeFunc) { // quick feedback to user if there is a typo or missconfiguration
                     throw new Error("serializeJSON ERROR: Invalid type " + type + " found in input name '" + name + "', please use one of " + objectKeys(typeFunctions).join(", "));
                 }
                 return typeFunc(strVal, el);
             },
     
             // Splits a field name into the name and the type. Examples:
             //   "foo"           =>  ["foo", ""]
             //   "foo:boolean"   =>  ["foo", "boolean"]
             //   "foo[bar]:null" =>  ["foo[bar]", "null"]
             splitType : function(name) {
                 var parts = name.split(":");
                 if (parts.length > 1) {
                     var t = parts.pop();
                     return [parts.join(":"), t];
                 } else {
                     return [name, ""];
                 }
             },
     
             // Check if this input should be skipped when it has a falsy value,
             // depending on the options to skip values by name or type, and the data-skip-falsy attribute.
             shouldSkipFalsy: function(name, nameSansType, type, el, opts) {
                 var skipFromDataAttr = $(el).attr("data-skip-falsy");
                 if (skipFromDataAttr != null) {
                     return skipFromDataAttr !== "false"; // any value is true, except the string "false"
                 }
     
                 var optForFields = opts.skipFalsyValuesForFields;
                 if (optForFields && (optForFields.indexOf(nameSansType) !== -1 || optForFields.indexOf(name) !== -1)) {
                     return true;
                 }
     
                 var optForTypes = opts.skipFalsyValuesForTypes;
                 if (optForTypes && optForTypes.indexOf(type) !== -1) {
                     return true;
                 }
     
                 return false;
             },
     
             // Split the input name in programatically readable keys.
             // Examples:
             // "foo"              => ["foo"]
             // "[foo]"            => ["foo"]
             // "foo[inn][bar]"    => ["foo", "inn", "bar"]
             // "foo[inn[bar]]"    => ["foo", "inn", "bar"]
             // "foo[inn][arr][0]" => ["foo", "inn", "arr", "0"]
             // "arr[][val]"       => ["arr", "", "val"]
             splitInputNameIntoKeysArray: function(nameWithNoType) {
                 var keys = nameWithNoType.split("["); // split string into array
                 keys = $.map(keys, function (key) { return key.replace(/\]/g, ""); }); // remove closing brackets
                 if (keys[0] === "") { keys.shift(); } // ensure no opening bracket ("[foo][inn]" should be same as "foo[inn]")
                 return keys;
             },
     
             // Set a value in an object or array, using multiple keys to set in a nested object or array.
             // This is the main function of the script, that allows serializeJSON to use nested keys.
             // Examples:
             //
             // deepSet(obj, ["foo"], v)               // obj["foo"] = v
             // deepSet(obj, ["foo", "inn"], v)        // obj["foo"]["inn"] = v // Create the inner obj["foo"] object, if needed
             // deepSet(obj, ["foo", "inn", "123"], v) // obj["foo"]["arr"]["123"] = v //
             //
             // deepSet(obj, ["0"], v)                                   // obj["0"] = v
             // deepSet(arr, ["0"], v, {useIntKeysAsArrayIndex: true})   // arr[0] = v
             // deepSet(arr, [""], v)                                    // arr.push(v)
             // deepSet(obj, ["arr", ""], v)                             // obj["arr"].push(v)
             //
             // arr = [];
             // deepSet(arr, ["", v]          // arr => [v]
             // deepSet(arr, ["", "foo"], v)  // arr => [v, {foo: v}]
             // deepSet(arr, ["", "bar"], v)  // arr => [v, {foo: v, bar: v}]
             // deepSet(arr, ["", "bar"], v)  // arr => [v, {foo: v, bar: v}, {bar: v}]
             //
             deepSet: function (o, keys, value, opts) {
                 if (opts == null) { opts = {}; }
                 var f = $.serializeJSON;
                 if (isUndefined(o)) { throw new Error("ArgumentError: param 'o' expected to be an object or array, found undefined"); }
                 if (!keys || keys.length === 0) { throw new Error("ArgumentError: param 'keys' expected to be an array with least one element"); }
     
                 var key = keys[0];
     
                 // Only one key, then it's not a deepSet, just assign the value in the object or add it to the array.
                 if (keys.length === 1) {
                     if (key === "") { // push values into an array (o must be an array)
                         o.push(value);
                     } else {
                         o[key] = value; // keys can be object keys (strings) or array indexes (numbers)
                     }
                     return;
                 }
     
                 var nextKey = keys[1]; // nested key
                 var tailKeys = keys.slice(1); // list of all other nested keys (nextKey is first)
     
                 if (key === "") { // push nested objects into an array (o must be an array)
                     var lastIdx = o.length - 1;
                     var lastVal = o[lastIdx];
     
                     // if the last value is an object or array, and the new key is not set yet
                     if (isObject(lastVal) && isUndefined(f.deepGet(lastVal, tailKeys))) {
                         key = lastIdx; // then set the new value as a new attribute of the same object
                     } else {
                         key = lastIdx + 1; // otherwise, add a new element in the array
                     }
                 }
     
                 if (nextKey === "") { // "" is used to push values into the nested array "array[]"
                     if (isUndefined(o[key]) || !isArray(o[key])) {
                         o[key] = []; // define (or override) as array to push values
                     }
                 } else {
                     if (opts.useIntKeysAsArrayIndex && isValidArrayIndex(nextKey)) { // if 1, 2, 3 ... then use an array, where nextKey is the index
                         if (isUndefined(o[key]) || !isArray(o[key])) {
                             o[key] = []; // define (or override) as array, to insert values using int keys as array indexes
                         }
                     } else { // nextKey is going to be the nested object's attribute
                         if (isUndefined(o[key]) || !isObject(o[key])) {
                             o[key] = {}; // define (or override) as object, to set nested properties
                         }
                     }
                 }
     
                 // Recursively set the inner object
                 f.deepSet(o[key], tailKeys, value, opts);
             },
     
             deepGet: function (o, keys) {
                 var f = $.serializeJSON;
                 if (isUndefined(o) || isUndefined(keys) || keys.length === 0 || (!isObject(o) && !isArray(o))) {
                     return o;
                 }
                 var key = keys[0];
                 if (key === "") { // "" means next array index (used by deepSet)
                     return undefined;
                 }
                 if (keys.length === 1) {
                     return o[key];
                 }
                 var tailKeys = keys.slice(1);
                 return f.deepGet(o[key], tailKeys);
             }
         };
     
         // polyfill Object.keys to get option keys in IE<9
         var objectKeys = function(obj) {
             if (Object.keys) {
                 return Object.keys(obj);
             } else {
                 var key, keys = [];
                 for (key in obj) { keys.push(key); }
                 return keys;
             }
         };
     
         var isObject =          function(obj) { return obj === Object(obj); }; // true for Objects and Arrays
         var isUndefined =       function(obj) { return obj === void 0; }; // safe check for undefined values
         var isValidArrayIndex = function(val) { return /^[0-9]+$/.test(String(val)); }; // 1,2,3,4 ... are valid array indexes
         var isArray =           Array.isArray || function(obj) { return Object.prototype.toString.call(obj) === "[object Array]"; };
     }));
     
               </script>
     <!-- serialize json script end -->
</body>

</html>