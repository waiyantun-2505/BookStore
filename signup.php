<?php
include('navbar.php');
include('connection.php');
$_SESSION['message']='';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (strlen($_POST['txtPassword']) < 8) {
		$_SESSION['message']='Password should be more than 8 characters!';
	}else{
		if($_POST['txtPassword'] == $_POST['txtConfirmPassword']){
			$fullName = $_POST['txtFullName'];
			$email = $_POST['txtEmail'];
			$password = $_POST['txtPassword'];
			$phone = $_POST['txtPhone'];
			$address = $_POST['txtAddress'];
			$cardType = $_POST['txtCardType'];
			$cardNo = $_POST['txtCardNo'];
			$sql = "INSERT INTO Customer (userName, email, password, address, cardType, cardNo, mobile)
			VALUES ('$fullName', '$email', '$password', '$address', '$cardType', '$cardNo', '$phone')";
			$result = mysqli_query($conn, $sql);
			if($result){
				echo "<script>window.alert('Customer Account Created!')</script>";
				echo "<script>window.location=('signin.php')</script>";
				//header("location: signin.php");
			} else {
				$_SESSION['message']='Fail creating user account!';
				echo "Error: " . mysqli_error($conn);
			}
		} else {
			$_SESSION['message']='Two password do not match!';
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Sign Up to Booklet</title>
</head>
<body>
	<form action="signup.php" method="post">
		<table align="center" cellspacing="30">
			<tr>
				<td>Full Name</td>
				<td class="padd">
					<input type="text" class="form-control" name="txtFullName" placeholder="John Smith" required/>
				</td>
			</tr>
			<tr>
				<td>Email</td>
				<td class="padd">
					<input type="email" class="form-control" name="txtEmail" placeholder="example@gmail.com" required/>
				</td>
			</tr>
			<tr>
				<td>Password</td>
				<td class="padd">
					<input type="password" class="form-control" name="txtPassword" placeholder="*****" required/>
				</td>
			</tr>
			<tr>
				<td>Confirm Password</td>
				<td class="padd">
					<input type="password" class="form-control" name="txtConfirmPassword" placeholder="*****" required/>
				</td>
			</tr>
			<td>Phone</td>
			<td class="padd">
				<input type="text" class="form-control" name="txtPhone" placeholder="+95912345678" required/>
			</td>
		</tr>
		<tr>
			<td>Address</td>
			<td class="padd">
				<input type="text" class="form-control" name="txtAddress" placeholder="[No. / Street / Township]" required/>
			</td>
		</tr>
		<tr>
			<td>Card Type</td>
			<td class="padd">
				<input type="text" class="form-control" name="txtCardType" placeholder="Visa/Master" required/>
			</td>
		</tr>
		<tr>
			<td>Card Number</td>
			<td class="padd">
				<input type="text" class="form-control" name="txtCardNo" placeholder="82458479849834" required/>
			</td>
		</tr>
		<tr>
			<td class="padd" colspan="2" align="center">
				<img src="generatecaptcha.php?rand=<?php echo rand(); ?>" id='captchaimg'/>
				<a href='javascript: refreshCaptcha();'>Refresh</a>
				<script Language='javascript' type='text/javascript'>
				function refreshCaptcha()
				{
					var img=document.images['captchaimg'];
					img.src=img.src.substring(0, img.src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
				}

				</script>
			</td>
		</tr>
		<tr>
			<td>Security Text</td>
			<td class="padd">
				<input type="text" class="form-control" name="code" id="code" placeholder="Enter Security Answer" required/>
			</td>
		</tr>
		<tr>
			<td></td>
			<td class="padd">
				<input type="submit" class="btn btn-primary" name="btnsubmit" value="Submit"/>
				<input type="reset" class="btn btn-primary" value="Clear"/>
			</td>
		</tr>
		<tr>
			<td></td>
			<td><span style="color:red"><?php echo $_SESSION['message'] ?></span></td>
		</tr>
	</table>
</form>
</body>
</html>
