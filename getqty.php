<?php
include('connection.php');
  $bookID = $_POST['bid'];
  $sql = "SELECT quantity FROM Book where bookID = '$bookID'";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $qtyy = $row['quantity'];
    echo $qtyy;
  } else {
    echo 0;
  }
 ?>
