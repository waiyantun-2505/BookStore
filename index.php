<?php
include('navbar.php');
include('connection.php');
?>
<!DOCTYPE html>
<html>
<head>
	<title>Booklet</title>
</head>
<body>
	<?php
	$sql = "SELECT * FROM Book";
	$result = mysqli_query($conn, $sql);
	$rowcount = mysqli_num_rows($result);

	if ($rowcount > 0) {
		while($row = mysqli_fetch_assoc($result)) {
			?>
			<div class="col-sm-6 col-md-4">
				<div class="thumbnail">
					<img src="img/<?php echo $row['frontImg'] ?>" width="150">
					<div class="caption">
						<h3><?php echo $row["title"] ?></h3>
						<h4 style="color:green">$ <?php echo $row["sellingPrice"] ?></h4>
						<p style="height:60px; line-height:20px; overflow:hidden"><?php echo $row["description"] ?></p>
						<p align="right"><a href="bookdetail.php?bookID=<?php echo $row['bookID']?>" class="btn btn-primary" role="button">More Detail</a></p>
					</div>
				</div>
			</div>
			<?php
		}
	} else {
		echo "<script>window.alert('No book added yet!')</script>";
		echo "<script>window.location='index.php'</script>";
	}
	?>
</body>
</html>
