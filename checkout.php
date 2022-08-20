<?php
include('navbar.php');
include('connection.php');

$userID = $_SESSION['uid'];
if(empty($userID)){
  echo "<script>window.alert('Please Login First');</script>";
  echo "<script>window.location='signin.php';</script>";
}else{
  $totalQty = $_SESSION['totalQty'];
  $totalAmt = $_SESSION['totalAmt'];
  $tax = $totalAmt * 0.05;
  $shipCharges = 10;

  $sql = "SELECT address FROM Customer where userID=" . $userID;
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $address = $row["address"];

  } else {
    echo "Fail retrieving address!";
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $today = date("Y-m-d");
    $now = date("h:i:sa");
    $payType = $_POST['txtPayType'];
    $paid = true;
    $shippingAddress = $_POST['txtAddress'];
    $township = $_POST['txtTsp'];
    $city = $_POST['txtCity'];
    $shipType = $_POST['txtShipType'];
    $delivery = true;
    $deliveryStatus = false;

    $sql = "INSERT INTO Sales (customerID, salesDate, salesTime, totalQty, totalAmount, paymentType, paid,
      shippingAddress, township, city, shippingType, delivery, deliveryStatus)
      VALUES ('$userID', '$today', '$now', '$totalQty', '$totalAmt', '$payType', '$paid',
        '$shippingAddress', '$township', '$city', '$shipType', '$delivery', '$deliveryStatus')";

        if (mysqli_query($conn, $sql)) {
          $last_id = mysqli_insert_id($conn);
          $_SESSION['OrderID']=$last_id;
          $_SESSION['voucher']=$sql;
          if(isset($_SESSION['cart'])){
            $successCnt=0;
            foreach ($_SESSION['cart'] as $citem) {
              $bookID = $citem['bid'];
              $qty = $citem['qty'];

              $sql1 = "SELECT quantity FROM Book where bookID='$bookID'";
              $result1 = mysqli_query($conn, $sql1);
              $row1 = mysqli_fetch_assoc($result1);
              $newqty = $row1['quantity']-$qty;

              $sql2 = "UPDATE Book SET quantity='$newqty' WHERE bookID='$bookID'";

              if (mysqli_query($conn, $sql2)) {
                //echo "Record updated successfully";
              } else {
                echo "Error updating quantity: " . mysqli_error($conn);
              }

              $sql = "INSERT INTO BookSales
              VALUES ('$last_id', '$bookID', '$qty')";

              if (mysqli_query($conn, $sql)) {
                $successCnt++;
              } else {
                echo "Error: " . $sql . "<br>" . mysqli_error($conn);
              }
            }
            if(sizeof($_SESSION['cart']) == $successCnt){
              echo "<h1 style='padding: 150px; color: #913d88'>You have made your order successfully. You order will be delivered within 3 working days. Thanks for shopping with us.</h1>";
              echo "<script>window.location='PrintVoucher.php'</script>";
              unset($_SESSION["cart"]);
            }
          }
        } else {
          echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
      }
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
      <meta charset="utf-8">
      <title>Checkout Product</title>
      <!-- <script src="https://www.paypalobjects.com/api/checkout.js"></script> -->
      <script src="js/checkout.js"></script>
      <script>
      paypal.Button.render({

        env: 'production', // Optional: specify 'sandbox' environment

        payment: function() {
          // Set up the payment here, when the buyer clicks on the button
        },

        onAuthorize: function(data, actions) {
          // Execute the payment here, when the buyer approves the transaction
        }

      }, '#paypal-button');
      </script>
    </head>
    <body>
      <form action="checkout.php" method="post">
        <div class="col-md-4" style="padding:0px 50px 0px 50px">
          <h4 align="center">Shipping Address</h4>
          <div class="form-group">
            <label class="control-label">Shipping Address</label>
            <textarea name="txtAddress" class="form-control" rows="3" placeholder="Shipping Address..."> <?php echo $address ?></textarea>
          </div>
          <div class="form-group">
            <label class="control-label">Township</label>
            <input type="text" class="form-control" name="txtTsp" placeholder="Dagon" value="Dagon">
          </div>
          <div class="form-group">
            <label class="control-label">City</label>
            <input type="text" class="form-control" name="txtCity" placeholder="Yangon" value="Yangon">
          </div>
          <div class="form-group">
            <label class="control-label">Shipping Type</label>
            <!-- <div class="radio">
              <label>
                <input type="radio" name="txtShipType" value="regular" checked>
                Regular (4 to 7 days) $6
              </label>
            </div> -->
            <div class="radio" style="margin-top:1px">
              <label>
                <input type="radio" name="txtShipType" value="express" checked>
                Express (1 to 3 days) $10
              </label>
            </div>
          </div>
        </div>
        <div class="col-md-4" style="padding:0px 50px 0px 50px">
          <h4 align="center">Payment Method</h4>
          <div class="form-group">
            <label class="control-label">Payment Type</label>
            <div class="radio">
              <label>
                <input type="radio" name="txtPayType" value="cash" checked>
                Cash on Delivery
              </label>
              <label style="margin-left:50px;">
                <input type="radio" name="txtPayType" value="credit">
                Credit Card
              </label>
            </div>
          </div>
          <div class="form-group">
            <label class="control-label">Card Type</label>
            <select class="form-control" name="txtCardType">
              <option value="Visa">Visa</option>
              <option value="Master">Master</option>
              <option value="Myanmar">Global Pay</option>
            </select>
          </div>
          <div class="form-group">
            <label class="control-label">Card Number</label>
            <input type="text" class="form-control" name="txtCardNo">
          </div>
          <div class="form-group">
            <label class="control-label">Expiry Date</label>
            <input type="date" class="form-control" name="txtExpiryDate">
          </div>
          <div class="form-group">
            <label class="control-label">Card Verification Number</label>
            <input type="number" class="form-control" name="txtCardVeri">
          </div>
          <!-- <button type="button" class="btn btn-primary" name="button" id="paypal-button">Make Payment</button> -->
          <div id="paypal-button"></div>
        </div>
        <div class="col-md-4" style="padding:0px 50px 0px 50px">
          <h4 align="center">Order Summary</h4>
          <br>
          <table class="table" style="padding:300px">
            <tr>
              <td>Total Item:</td>
              <td class="alright"><span class="badge"><?php echo $totalQty ?></span></td>
            </tr>
            <tr>
              <td>Sub Total:</td>
              <td class="alright"><?php echo $totalAmt ?></td>
            </tr>
            <tr>
              <td>Tax(5%):</td>
              <td class="alright"><?php echo $tax ?></td>
            </tr>
            <tr>
              <td>Shipping Charges:</td>
              <td class="alright"><?php echo $shipCharges ?></td>
            </tr>
            <tr style="border: 2px solid black; border-bottom:0; border-left:0; border-right:0">
              <td style="font-weight: bold">Grand Total:</td>
              <td class="alright" style="font-weight: bold"><?php echo $totalAmt + $tax + $shipCharges ?></td>
            </tr>
          </table>
          <div align="center">
            <input type="submit" class="btn btn-success" name="btnOrder" value="Place your order">
          </div>
        </div>
      </form>
    </body>
    </html>
