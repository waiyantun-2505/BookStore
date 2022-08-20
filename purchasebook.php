<?php
include('admin_navbar.php');
include('connection.php');
$_SESSION['message'];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (isset($_POST['btnadd'])) {
    $supplier = $_POST['txtSupplier'];
    $_SESSION['supplier'] = $supplier;
    $staff = $_POST['txtStaff'];
    $_SESSION['staff'] = $staff;
    $bookID = $_POST['txtBooks'];

    $sql = "SELECT title,purchasedPrice FROM Book where bookID='$bookID'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    $title = $row["title"];
    $purPrice = $row['purchasedPrice'];
    $qty = $_POST['txtPurQty'];
    $purDate = $_POST['txtPurDate'];

    //$item = array("bid"=>$bookID , "title"=>$title , "qty"=>$qty , "sprice"=>$selPrice , "pprice"=>$purPrice);
    $itemList1 = array("bid".$bookID => array("bid"=>$bookID , "supplier"=>$supplier, "title"=>$title , "qty"=>$qty , "purdate"=>$purDate , "price"=>$purPrice));

    if(!empty($_SESSION['purchase'])){
      if(in_array("bid".$bookID,array_keys($_SESSION["purchase"]))) {
        foreach($_SESSION["purchase"] as $k => $v) {
          if("bid".$bookID == $k) {
            $_SESSION["purchase"][$k]["qty"] += $qty;
          }
        }
      } else {
        $_SESSION["purchase"] = array_merge($_SESSION["purchase"],$itemList1);
      }
    }else{
      $_SESSION['purchase'] = $itemList1;
    }
  }
  elseif (isset($_POST['btnconfirm'])) {
    $today = date("Y-m-d");
    $supplier = $_SESSION['supplier'];
    $staff = $_SESSION['staff'];
    $totalQty = $_SESSION['ptotalQty'];
    $totalAmt = $_SESSION['ptotalAmt'];

    $sql = "INSERT INTO Purchase (purchaseDate, supplierName, totalQty, totalAmount, staffName)
    VALUES ('$today', '$supplier', '$totalQty', '$totalAmt', '$staff')";

    if (mysqli_query($conn, $sql)) {
      $last_id = mysqli_insert_id($conn);
      if(isset($_SESSION['purchase'])){
        $successCnt = 0;
        foreach ($_SESSION['purchase'] as $pitem) {
          $bookID = $pitem['bid'];
          $qty = $pitem['qty'];

          $sql1 = "SELECT quantity FROM Book where bookID='$bookID'";
          $result1 = mysqli_query($conn, $sql1);
          $row1 = mysqli_fetch_assoc($result1);
          $newqty = $qty+$row1['quantity'];

          $sql2 = "UPDATE Book SET quantity='$newqty' WHERE bookID='$bookID'";

          if (mysqli_query($conn, $sql2)) {
            //echo "Record updated successfully";
          } else {
            $_SESSION['message']= "Error updating quantity: " . mysqli_error($conn);
          }

          $sql = "INSERT INTO BookPurchase
          VALUES ('$last_id', '$bookID', '$qty')";

          if (mysqli_query($conn, $sql)) {
            //echo "New Purchase created successfully";
            $successCnt++;
          } else {
            $_SESSION['message']= "Error: " . $sql . "<br>" . mysqli_error($conn);
          }
        }
        if(sizeof($_SESSION['purchase']) == $successCnt){
          $_SESSION['message']= "New Purchase created successfully";
          unset($_SESSION["purchase"]);
        }
      }
    } else {
      $_SESSION['message']= "Error: " . $sql . "<br>" . mysqli_error($conn);
    }
  }
}

if(isset($_GET["action"])){
  if($_GET["action"] == "remove"){

    if(!empty($_SESSION["purchase"])) {
      foreach($_SESSION["purchase"] as $k => $v) {
        if("bid".$_GET["bid"] == $k)
        unset($_SESSION["purchase"][$k]);
        if(empty($_SESSION["purchase"]))
        unset($_SESSION["purchase"]);
      }
    }
  }
  if($_GET["action"] == "empty"){
    unset($_SESSION["purchase"]);
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Purchase Book</title>
</head>
<body>
  <form action="purchasebook.php" method="post">
    <table align="center" cellspacing="30">
      <tr>
        <td>Supplier</td>
        <td class="padd">
          <input type="text" class="form-control" name="txtSupplier" placeholder="Supplier Name" required/>
        </td>
      </tr>
      <tr>
        <td>Book List</td>
        <td class="padd">
          <select class="form-control" id="slBooks" name="txtBooks">
            <option value="0">-</option>
            <?php
            $sql = "select * from Book";
            $result = mysqli_query($conn, $sql);

            if (mysqli_num_rows($result) > 0) {
              while($row = mysqli_fetch_assoc($result)) {
                echo "<option value=" . $row['bookID'] . ">" . $row['bookID'] . ". " . $row["title"] . "</option>";
              }
            } else {
              echo "<option value='0'>-</option>";
            }
            ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>Remaining Quantity</td>
        <td class="padd">
          <input type="text" id="txtrqty" class="form-control" name="txtRemainQty" readonly="true" required/>
        </td>
      </tr>
      <tr>
        <td>Purchase Quantity</td>
        <td class="padd">
          <input type="number" class="form-control" value="1" name="txtPurQty" required/>
        </td>
      </tr>
      <tr>
        <td>Purchase Date</td>
        <td class="padd">
          <input type="text" class="form-control" id="datepicker" name="txtPurDate" value="<?php echo date('Y-m-d');?>" required/>
        </td>
      </tr>
      <tr>
        <td>Staff Name</td>
        <td class="padd">
          <input type="text" class="form-control" name="txtStaff" value="<?php echo $_SESSION['adname'] ?>" required/>
        </td>
      </tr>
      <tr>
        <td></td>
        <td class="padd">
          <input type="submit" name="btnadd" value="Add" class="btn btn-primary">
          <span style="color:red"><?php echo $_SESSION['message'] ?></span>
        </td>
      </tr>
    </table>
    <div style="padding-top:50px;padding-right:50px;padding-left:50px">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>No</th>
            <th>Supplier</th>
            <th>Item</th>
            <th>Quantity</th>
            <th>Amount</th>
            <th>Action</th>
          </tr>
        </thead>
        <?php
        if(isset($_SESSION['purchase'])){
          $totalamt = 0;
          $totalqty = 0;
          $counter = 1;
          foreach ($_SESSION['purchase'] as $citem) {
            $amount = $citem['qty']*$citem['price'];
            $totalamt += $amount;
            $totalqty += $citem['qty'];
            echo "<tr>";
            echo "<td>".$counter."</td>";
            echo "<td>".$citem['supplier']."</td>";
            echo "<td>".$citem['title']."</td>";
            echo "<td>".$citem['qty']."</td>";
            echo "<td>".$amount."</td>";
            echo "<td><a href='purchasebook.php?action=remove&bid=" . $citem['bid'] . "'>Remove Item</a></td>";
            echo "</tr>";
            $counter++;
          }
          $_SESSION['ptotalQty'] = $totalqty;
          $_SESSION['ptotalAmt'] = $totalamt;
        }
        ?>
      </table>
    </div>
  </form>
  <form action="purchasebook.php" method="post">
    <div class="col-md-5"></div>
    <div class="col-md-3">
      <h3>Total of <?php if(isset($_SESSION['purchase'])){ echo $totalqty; } ?> Item - $
        <?php if(isset($_SESSION['purchase'])){ echo $totalamt; } ?></h3>
      </div>
      <div class="col-md-4" style="padding-top: 15px">
        <input type="submit" class="btn btn-success" name="btnconfirm" value="Confirm">
        <span style="padding-left:10px"><a href='purchasebook.php?action=empty' class='btn btn-danger'>Cancel</a></span>
      </div>
    </form>

    <script type="text/javascript">
    $("#slBooks").change(function(){
      var bid = $("#slBooks").val();
      $.ajax({
        type: "POST",
        url: "getqty.php",
        data: {bid:bid},
        success: function(response){
          $("#txtrqty").val(response);
        },
        error: function(jqXHR, textStatus, errorThrown) {
           console.log(textStatus, errorThrown);
        }
      });
    });
    </script>

  </body>
  </html>
