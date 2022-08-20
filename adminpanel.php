<?php
include('admin_navbar.php');
if(!isset($_SESSION['adid'])){
  echo "<script>window.location='adminsignin.php'</script>";
}
 ?>
 <!DOCTYPE html>
 <html>
   <head>
     <meta charset="utf-8">
     <title>Admin Panel</title>
   </head>
   <body style="padding:60px">
     <h2>Admin Panel</h2>
     <div class="col-sm-4" style="padding-left:0px">
       <ul class="list-group">
         <li class="list-group-item"><a href="registerbook.php">Register Book</a></li>
         <li class="list-group-item"><a href="registerauthor.php">Register Author</a></li>
         <li class="list-group-item"><a href="purchasebook.php">Purchase Book</a></li>
         <li class="list-group-item"><a href="salesreport.php">Sales Report</a></li>

       </ul>
     </div>
   </body>
 </html>
