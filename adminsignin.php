<?php
include('navbar.php');
include('connection.php');
$_SESSION['message']='';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$adminName = $_POST['txtID'];
		$password = $_POST['txtPassword'];
		$sql = "SELECT adminID,adminName FROM Admin Where adminName='$adminName' And password='$password'";
		$result = mysqli_query($conn, $sql);
		$rowcount = mysqli_num_rows($result);

if ($rowcount > 0) {
    $row = mysqli_fetch_assoc($result);
		$_SESSION['adid']=$row["adminID"];
		$_SESSION['adname']=$row["adminName"];
		echo "<script>window.location='adminpanel.php'</script>";
} else {
	echo "<script>window.alert('Invalid Account Information.')</script>";
	echo "<script>window.location='adminsignin.php'</script>";
}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Sign In to Admin Panel</title>
	</head>
	<body style="padding-top: 70px">
		<form action="adminsignin.php" method="post">
			<table align="center" cellspacing="3">
				<tr>
					<td>Admin Name:</td>
					<td class="padd">
						<input type="text" class="form-control" name="txtID" placeholder="manager" required/>
					</td>
				</tr>
				<tr>
					<td>Password:</td>
					<td class="padd">
						<input type="password" class="form-control" name="txtPassword" placeholder="******" required/>
					</td>
				</tr>
				<tr>
					<td></td>
					<td class="padd">
						<input type="submit" class="btn btn-primary" name="btnsignin" value="Sign In"/>
						<input type="reset" class="btn btn-primary" value="Clear" />
					</td>
				</tr>
			</table>
		</form>
	</body>
</html>
