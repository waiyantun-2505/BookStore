<?php
include('navbar.php');
include('connection.php');
$searchText = $_POST['txtSearch'];
$sql = "SELECT * FROM Book WHERE title LIKE '%$searchText%'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
      ?>
      <div class="col-sm-6 col-md-4">
        <div class="thumbnail">
          <img src="img/<?php echo $row['frontImg'] ?>" width="150">
          <div class="caption">
            <h3><?php echo $row["title"] ?></h3>
            <p><?php echo $row["description"] ?></p>
            <p><a href="bookdetail.php?bookID=<?php echo $row['bookID']?>" class="btn btn-primary" role="button">Detail</a></p>
          </div>
        </div>
      </div>
      <?php
    }
} else {
    echo "0 results";
}
 ?>
