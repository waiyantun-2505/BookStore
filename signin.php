<?php
include('navbar.php');
include('connection.php');
$_SESSION['message']='';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
		$email = $_POST['txtEmail'];
		$password = $_POST['txtPassword'];
		$sql = "SELECT userID, userName FROM Customer Where email='$email' And password='$password'";
		$result = mysqli_query($conn, $sql);
		$rowcount = mysqli_num_rows($result);

if ($rowcount > 0) {
    $row = mysqli_fetch_assoc($result);
		$_SESSION['uid']=$row["userID"];
		$_SESSION['uname']=$row["userName"];
		echo "<script>window.location='index.php'</script>";
} else {
	echo "<script>window.alert('Invalid Account Information.')</script>";
	echo "<script>window.location='signin.php'</script>";
}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Sign In to Booklet</title>
	</head>
	<body style="padding-top: 70px">
		<form action="signin.php" method="post">
			<table align="center" cellspacing="3">
				<tr>
					<td>Email:</td>
					<td class="padd">
						<input type="email" class="form-control" name="txtEmail" placeholder="Enter Email Address" required/>
					</td>
				</tr>
				<tr>
					<td>Password:</td>
					<td class="padd">
						<input type="password" class="form-control" name="txtPassword" placeholder="*****" required/>
					</td>
				</tr>
				<tr>
					<td></td>
					<td class="padd">
						<input type="submit" class="btn btn-primary" name="btnsignin" value="Sign In"/>
						<input type="reset" class="btn btn-primary" value="Clear" />
					</td>
					<tr>
						<td></td>
						<td class="padd">Dont't have an Account?<a href='signup.php'> Click here to register</a></td>
					</tr>
				</tr>
			</table>
		</form>
	</body>
</html>
