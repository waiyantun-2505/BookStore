<?php
include('navbar.php');
include('connection.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $bookID = $_POST['txtBookID'];
  $title = $_POST['txtTitle'];
  $qty = $_POST['txtQty'];
  $selPrice = $_POST['txtSelPrice'];
  $purPrice = $_POST['txtPurPrice'];
  //$item = array("bid"=>$bookID , "title"=>$title , "qty"=>$qty , "sprice"=>$selPrice , "pprice"=>$purPrice);
  $itemList = array("bid".$bookID => array("bid"=>$bookID , "title"=>$title , "qty"=>$qty , "sprice"=>$selPrice , "pprice"=>$purPrice));

  if(!empty($_SESSION['cart'])){
    if(in_array("bid".$bookID,array_keys($_SESSION["cart"]))) {
      foreach($_SESSION["cart"] as $k => $v) {
        if("bid".$bookID == $k) {
          $_SESSION["cart"][$k]["qty"] += $qty;
        }
      }
    } else {
      $_SESSION["cart"] = array_merge($_SESSION["cart"],$itemList);
    }
  }else{
    $_SESSION['cart'] = $itemList;
  }
}
if(isset($_GET["action"])){
  if($_GET["action"] == "remove"){

    if(!empty($_SESSION["cart"])) {
    			foreach($_SESSION["cart"] as $k => $v) {
    					if("bid".$_GET["bid"] == $k)
    						unset($_SESSION["cart"][$k]);
    					if(empty($_SESSION["cart"]))
    						unset($_SESSION["cart"]);
    			}
    		}
  }
  if($_GET["action"] == "empty"){
    unset($_SESSION["cart"]);
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Shopping Cart</title>
</head>
<body>
  <form action="checkout.php" method="post" style="padding: 30px">
    <table class="table table-hover">
      <tr>
        <th>No</th>
        <th>Item</th>
        <th>Quantity</th>
        <th>Price</th>
        <th>Amount</th>
        <th>Action</th>
      </tr>
      <?php
      if(isset($_SESSION['cart'])){
        $totalamt = 0;
        $totalqty = 0;
        $counter = 1;
        foreach ($_SESSION['cart'] as $citem) {
          if($citem['qty']<100){
            $amount = $citem['qty']*$citem['sprice'];
          }else{
            $amount = $citem['qty']*$citem['pprice'];
          }
          $totalamt += $amount;
          $totalqty += $citem['qty'];
          echo "<tr>";
          echo "<td>".$counter."</td>";
          echo "<td>".$citem['title']."</td>";
          echo "<td>".$citem['qty']."</td>";
          echo "<td>".$citem['sprice']."</td>";
          echo "<td>".$amount."</td>";
          echo "<td><a href='cart.php?action=remove&bid=" . $citem['bid'] . "'>Remove Item</a></td>";
          echo "</tr>";
          $counter++;
        }
        $_SESSION['totalQty'] = $totalqty;
        $_SESSION['totalAmt'] = $totalamt;
      }
      ?>
    </table>
    <div class="row">
      <div class="col-md-5">
        <script>var pfHeaderImgUrl = '';var pfHeaderTagline = 'Order%20Report';var pfdisableClickToDel = 0;var pfHideImages = 0;
        var pfImageDisplayStyle = 'right';var pfDisablePDF = 0;var pfDisableEmail = 0;var pfDisablePrint = 0;var pfCustomCSS = '';
        var pfBtVersion='1';(function(){var js, pf;pf = document.createElement('script');pf.type = 'text/javascript';
        if('https:' == document.location.protocol){js='https://pf-cdn.printfriendly.com/ssl/main.js'}
        else{js='http://cdn.printfriendly.com/printfriendly.js'}pf.src=js;document.getElementsByTagName('head')[0].appendChild(pf)})();</script>

        <div style="padding-left:80px;padding-top:15px">
          <a href="http://www.printfriendly.com" style="color:#6D9F00;text-decoration:none;" class="printfriendly"
          onClick="window.print();return false;" title="Printer Friendly and PDF">
          <img style="border:none;-webkit-box-shadow:none;box-shadow:none;" src="css/images/button-print-grnw20.png"
          alt="Print Friendly and PDF"/></a>
        </div>

      </div>
      <div class="col-md-3">
        <?php
          if(isset($_SESSION['cart'])){
            echo "<h3>Total of $totalqty Item - $ $totalamt </h3>";
          }
         ?>
      </div>
      <div class="col-md-4" style="padding-top: 15px">
        <!-- <input type='submit' class='btn btn-success' name='btnSubmit' value='Checkout'> -->
        <a href='checkout.php' class='btn btn-primary'>Checkout</a>
        <a href='index.php' class='btn btn-warning'>Continue Shopping</a>
        <a href='cart.php?action=empty' class='btn btn-danger'>Empty Cart</a></span>
      </div>
    </div>
  </form>
</body>
</html>
