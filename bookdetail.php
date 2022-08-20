<?php
include('navbar.php');
include('connection.php');
$bookID = $_GET['bookID'];
$sql = "SELECT * FROM Book where bookID = '$bookID'";
$result = mysqli_query($conn, $sql);
$rowcount = mysqli_num_rows($result);

if ($rowcount > 0) {
  $row = mysqli_fetch_assoc($result);
  $title = $row['title'];
  $pubDate = $row['publishedDate'];
  $publisher = $row['publisher'];
  $desc = $row['description'];
  $qty = $row['quantity'];
  $purPrice = $row['purchasedPrice'];
  $sellPrice = $row['sellingPrice'];
  $edition = $row['edition'];
  $frontImg = $row['frontImg'];

  $sql2 = "select authorName from Author a inner join BookAuthor ba on a.authorID=ba.authorID
            inner join Book b on b.bookID=ba.bookID where b.bookID=$bookID";
  $result2 = mysqli_query($conn, $sql2);

  if (mysqli_num_rows($result2) > 0) {
    $row2 = mysqli_fetch_assoc($result2);
    $author = $row2['authorName'];
  }
} else {
  echo "<script>window.alert('Book information is not available!')</script>";
  echo "<script>window.location='index.php'</script>";
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Book Detail Information</title>
</head>
<body>
  <form class="paddlr" action="cart.php" method="post">
    <input type="hidden" name="txtBookID" id="txtbid" value="<?php echo $bookID ?>">
    <input type="hidden" name="txtTitle" value="<?php echo $title ?>">
    <input type="hidden" name="txtSelPrice" value="<?php echo $sellPrice ?>">
    <input type="hidden" name="txtPurPrice" value="<?php echo $purPrice ?>">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title" style="font-weight:bold"><?php echo $title ?></h3>
      </div>
      <div class="panel-body">
        <div class="row">
          <div class="col-md-4"><img src="img/<?php echo $frontImg ?>" width="250px"></div>
          <div class="col-md-8" style="padding-top: 20px">
            <h3 style="padding-left:15px">by <?php echo $author ?></h3>
            <p style="padding-left:15px"><?php echo $desc ?></p>
            <h4 style="padding-left:15px;color:#019875;font-weight:bold">$ <?php echo $sellPrice ?></h4>
            <div class="col-sm-2"><input type="number" name="txtQty" id="qtycontrol" class="form-control" value="1"></div>
            <div>
              <input type="submit" name="btnSubmit" class="btn btn-primary" value="Add to Cart">
            </div>
            <p id="txtStatus" style="color:red"></p>
          </div>
        </div>
      </div>
    </div>
  </form>
  <script type="text/javascript">
  $('#qtycontrol').on('input', function() {
    var bid = $("#txtbid").val();
    $.ajax({
      type: "POST",
      url: "getqty.php",
      data: {bid:bid},
      success: function(response){
        if( parseInt($("#qtycontrol").val()) > parseInt(response) ){
          $("#qtycontrol").val(response);
          $("#txtStatus").text("Not Enough Stock! Only "+response+" left.");
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {
         console.log(textStatus, errorThrown);
      }
    });
  });
  </script>
</body>
</html>
