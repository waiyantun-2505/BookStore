<?php
include('admin_navbar.php');
include('connection.php');
$_SESSION['message']='';
if(isset($_GET['mode'])){
	$authorID=$_GET['authorID'];
	$sql = "SELECT * FROM Author where authorID=$authorID";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		$authorName=$row['authorName'];
		$phone=$row['phone'];
		$email=$row['email'];
		$address=$row['address'];
	}
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST['btnsave']))
	{
		$sAuName=$_POST['txtAuName'];
		$sPhone=$_POST['txtPhone'];
		$sEmail=$_POST['txtEmail'];
		$sAddress=$_POST['txtAddress'];

		$sql = "INSERT INTO Author (authorName, phone, email, address)
		VALUES ('$sAuName', '$sPhone', '$sEmail','$sAddress')";

		if (mysqli_query($conn, $sql)) {
			$_SESSION['message'] = "New author created successfully";
		} else {
			$_SESSION['message'] = "Error: " . $sql . "<br>" . mysqli_error($conn);
		}
	}
	if (isset($_POST['btnupdate']))
	{
		$uAuName=$_POST['txtAuName'];
		$uPhone=$_POST['txtPhone'];
		$uEmail=$_POST['txtEmail'];
		$uAddress=$_POST['txtAddress'];
		$uAuID=$_POST['auID'];

		$sql = "UPDATE Author SET authorName='$uAuName', phone='$uPhone', email='$uEmail', address='$uAddress' WHERE authorID=$uAuID";

		if ($conn->query($sql) === TRUE) {
			$_SESSION['message'] = "Record updated successfully";
		} else {
			$_SESSION['message'] = "Error updating record: " . $conn->error;
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Register Author</title>
</head>
<body>
	<form class="form-horizontal" action="registerauthor.php" method="post">
		<div class="col-md-6" style="padding-left: 100px">
			<input type="hidden" name="auID" value="<?php echo $authorID ?>"/>
			<div class="form-group">
				<label class="col-sm-4 control-label">Author Name</label>
				<div class="col-sm-5">
					<input type="text" class="form-control" name="txtAuName" placeholder="Danny Rose" value="<?php if(isset($authorName)){echo $authorName;} ?>" required/>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Phone</label>
				<div class="col-sm-5">
					<input type="text" class="form-control" name="txtPhone" placeholder="00123456788" value="<?php if(isset($phone)){echo $phone;} ?>" required/>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email</label>
				<div class="col-sm-5">
					<input type="text" class="form-control" name="txtEmail" placeholder="danrose@gmail.com" value="<?php if(isset($email)){echo $email;} ?>" required/>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Address</label>
				<div class="col-sm-5">
					<textarea name="txtAddress" class="form-control" rows="3" placeholder="Mailing address of the author..."><?php if(isset($address)){echo $address;} ?></textarea>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-4 col-sm-5">
					<?php
					if(isset($_GET['mode']))
					{
						echo "<input type='submit' class='btn btn-primary' name='btnupdate' value='Update'/>";
					}
					else
					{
						echo "<input type='submit' class='btn btn-primary' name='btnsave' value='Save'/>";
					}
					?>
					<input type="reset" class="btn btn-primary" value="Clear"/>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-4 col-sm-5">
				<span style="color:red"><?php echo $_SESSION['message'] ?></span>
			</div>
			</div>
		</div>
	</form>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>ID</th>
				<th>Name</th>
				<th>Phone</th>
				<th>Email</th>
				<th>Address</th>
				<th>Action</th>
			</tr>
			<?php
			$sql = "SELECT * FROM Author";
			$result = mysqli_query($conn, $sql);

			if (mysqli_num_rows($result) > 0) {
				while($row = mysqli_fetch_assoc($result)) {
					$authID = $row["authorID"];
					echo "<tr>";
					echo "<td>" . $row["authorID"]. "</td>";
					echo "<td>" . $row["authorName"]. "</td>";
					echo "<td>" . $row["phone"]. "</td>";
					echo "<td>" . $row["email"]. "</td>";
					echo "<td>" . $row["address"]. "</td>";
					echo "<td><a href='registerauthor.php?authorID=$authID&mode=update'>Edit</a> |
					<a href='deleteauthor.php?authorID=$authID'>Delete</a> </td>";
					echo "</tr>";
				}
			} else {
				echo "0 results";
			}
			?>
		</thead>
	</table>
</body>
</html>
