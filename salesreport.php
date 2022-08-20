<?php
include('admin_navbar.php');
include('connection.php');
function salesPeriodReport($period){
  $sql = "";
  if($period == 0){
    $sql = "SELECT * FROM Sales WHERE YEAR(salesDate) = YEAR(NOW()) AND MONTH(salesDate) = MONTH(NOW()) AND DAY(salesDate) = DAY(NOW()) ORDER BY salesID";
  }else if($period == 1){
    $sql = "SELECT * FROM Sales WHERE WEEKOFYEAR(salesDate) = WEEKOFYEAR(NOW()) ORDER BY salesID";
  }else if($period == 2){
    $sql = "SELECT * FROM Sales WHERE YEAR(salesDate) = YEAR(NOW()) AND MONTH(salesDate)=MONTH(NOW()) ORDER BY salesID";
  }else if($period == 3){
    $sql = "SELECT * FROM Sales WHERE WEEKOFYEAR(salesDate) = WEEKOFYEAR(NOW())-1 ORDER BY salesID";
  }else if($period == 4){
    $sql = "SELECT * FROM Sales WHERE YEAR(salesDate) = YEAR(NOW()) AND MONTH(salesDate)=MONTH(NOW())-1 ORDER BY salesID";
  }
  $_SESSION['query'] = $sql;
  $result = mysqli_query($GLOBALS['conn'], $sql);

  if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
      $uid = $row['customerID'];
      $sql2 = "SELECT userName FROM Customer where userID=$uid";
      $result2 = mysqli_query($GLOBALS['conn'], $sql2);

      if (mysqli_num_rows($result2) > 0) {
        $row2 = mysqli_fetch_assoc($result2);
          $cName = $row2['userName'];
      }
      echo "<tr>";
      echo "<td>".$row['salesID']."</td>";
      echo "<td>".$cName."</td>";
      echo "<td>".$row['salesDate']."</td>";
      echo "<td>".$row['totalQty']."</td>";
      echo "<td>".$row['totalAmount']."</td>";
      echo "<td>".$paid = ($row['paid'] == 1 ? 'Yes' : 'No')."</td>";
      echo "<td>".$delivery = ($row['delivery'] == 1 ? 'Yes' : 'No')."</td>";
      echo "<td>".$deliverySts = ($row['deliveryStatus'] == 1 ? 'Delivered' : 'Not Delivered')."</td>";
      echo "<td>".$row['shippingType']."</td>";
      echo "<td>".$row['shippingAddress']."</td>";
      echo "</tr>";
    }
  } else {
    echo "0 result";
  }
}
function generateSalesReport($fromDate, $toDate){
  $sql = "SELECT * FROM Sales where salesDate between '$fromDate' and '$toDate' order by salesID";
  $_SESSION['query'] = $sql;
  $result = mysqli_query($GLOBALS['conn'], $sql);

  if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
      echo "<tr>";
      echo "<td>".$row['salesID']."</td>";
      echo "<td>".$row['customerID']."</td>";
      echo "<td>".$row['salesDate']."</td>";
      echo "<td>".$row['totalQty']."</td>";
      echo "<td>".$row['totalAmount']."</td>";
      echo "<td>".$row['paid']."</td>";
      echo "<td>".$row['delivery']."</td>";
      echo "<td>".$row['deliveryStatus']."</td>";
      echo "<td>".$row['shippingType']."</td>";
      echo "<td>".$row['shippingAddress']."</td>";
      echo "</tr>";
    }
  } else {
    echo "0 result";
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Sales Report</title>
  <script type="text/javascript">
  $(document).ready(function(){
    $('#idPeriod').val('<?php echo $_POST['txtPeriod'];?>');
    if($("#idPeriod").val() == 5){
      $("#idFrom").show();
      $("#idTo").show();
    }else{
      $("#idFrom").hide();
      $("#idTo").hide();
    }
  });
  </script>
</head>
<body>
  <form class="form-inline" action="salesreport.php" method="post" style="padding-left:30px;padding-right:30px">
    <div class="form-group">
      <label>Period</label>
      <select class="form-control" id="idPeriod" name="txtPeriod">
        <option value="0">Today</option>
        <option value="1">This week</option>
        <option value="2">This month</option>
        <option value="3">Last Week</option>
        <option value="4">Last Month</option>
        <option value="5">Custom Range</option>
      </select>
      <script type="text/javascript">
      $( "#idPeriod" ).change(function() {
        if($("#idPeriod").val() == 5){
          $("#idFrom").show();
          $("#idTo").show();
        }
      });
      </script>
    </div>
    <div class="form-group" id="idFrom">
      <label>From</label>
      <input type="text" class="form-control" id="datepicker" name="txtFromDate" value="<?php if(isset($_POST['txtFromDate'])){ echo $_POST['txtFromDate']; }?>">
    </div>
    <div class="form-group" id="idTo">
      <label>To</label>
      <input type="text" class="form-control" id="datepicker1" name="txtToDate" value="<?php if(isset($_POST['txtToDate'])){ echo $_POST['txtToDate']; }?>">
    </div>
    <button type="submit" class="btn btn-primary">Search</button>
    <div style="padding-top:20px">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Sales ID</th>
            <th>Customer</th>
            <th>Date</th>
            <th>Total Qty</th>
            <th>Total Amount</th>
            <th>Paid?</th>
            <th>Delivery</th>
            <th>Delivery Status</th>
            <th>Shipping Type</th>
            <th>Shipping Address</th>
          </tr>
          <?php
          if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $period = $_POST['txtPeriod'];
            if($period == 5){
              $fromDate = $_POST['txtFromDate'];
              $toDate = $_POST['txtToDate'];
              generateSalesReport($fromDate, $toDate);
            }else{
              salesPeriodReport($period);
            }
          }else{
            $fromDate = date("Y-m-d");
            $toDate = date("Y-m-d");
            ?>
            <script>
              $("#datepicker").val('<?php echo $fromDate ?>');
              $("#datepicker1").val('<?php echo $toDate ?>');
            </script>
            <?php
            generateSalesReport($fromDate, $toDate);
          }
          ?>
        </thead>
      </table>
    </div>
  </form>

  <form action="excelExporter.php" method="post" align="right" style="padding-right:50px">
    <input type="text" name="sql" value="<?php echo $_SESSION['query'] ?>" hidden>
    <input type="submit" class="btn btn-warning"name="btnexport" value="Export to Excel">
  </form>
  <script type="text/javascript">

  </script>
</body>
</html>
