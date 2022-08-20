<?php
include('admin_navbar.php');
include('connection.php');
$_SESSION['message']='';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$title = mysql_real_escape_string($_POST['txtTitle']);
	$category = $_POST['txtCategory'];
	$country = $_POST['txtCountry'];
	$language = $_POST['txtLanguage'];
	$pubDate = $_POST['txtPublishedDate'];
	$publisher = mysql_real_escape_string($_POST['txtPublisher']);
	$printed = $_POST['txtPrinted'];
	if(isset($_POST['txtEbook'])){ $ebook = $_POST['txtEbook']; }else{$ebook = "";}
	//$ebook = $_POST['txtEbook'];
	$mediaType = "printed";
	if($printed == "printed" && $ebook == ""){
		$mediaType = "printed";
	}else if($printed == "" && $ebook == "ebook"){
		$mediaType = "ebook";
	}else if($printed == "printed" && $ebook == "ebook"){
		$mediaType = "both";
	}
	$desc = mysql_real_escape_string($_POST['txtDesc']);
	$qty = $_POST['txtQty'];
	$purPrice = $_POST['txtPurchasedPrice'];
	$sellPrice = $_POST['txtSellingPrice'];
	$rating = "4";
	$edition = mysql_real_escape_string($_POST['txtEdition']);
	$author = $_POST['txtAuthor'];

	//File Upload Start
	$uploadCount=0;
	$totalImg = count($_FILES['txtImg']['name']);
	$valid_formats = array("jpg", "png", "gif", "jpeg", "bmp");

	foreach ($_FILES["txtImg"]["error"] as $key => $error) {
		if ($error == UPLOAD_ERR_OK) {
			$tmp_name = $_FILES["txtImg"]["tmp_name"][$key];
			// basename() may prevent filesystem traversal attacks;
			// further validation/sanitation of the filename may be appropriate
			$name = basename($_FILES["txtImg"]["name"][$key]);
			${"filename" . $uploadCount} = basename($_FILES["txtImg"]["name"][$key]);

			if ($_FILES['txtImg']['size'][$key] > 3000000) {
				$message[] = "$name is too large! Cannot be more than 3MB.";
				continue; // Skip large files
			}
			elseif( ! in_array(pathinfo($name, PATHINFO_EXTENSION), $valid_formats) ){
				$message[] = "$name is not a valid format";
				continue; // Skip invalid file formats
			}
			else{ // No error found! Move uploaded files
				if(move_uploaded_file($tmp_name, "img/$name")){
					$uploadCount++;
				}
			}
		}
	}
	echo "<script>window.alert('Total of $uploadCount image uploaded successfully!')</script>";
	//File Upload End

	if(isset($filename0)){ $frontImg = $filename0; }else{$frontImg = "";}
	if(isset($filename1)){ $backImg = $filename1; }else{$backImg = "";}
	if(isset($filename2)){ $extraImg = $filename2; }else{$extraImg = "";}

	$sql = "INSERT INTO Book (title, categoryID, country, language, publishedDate, publisher, mediaType, description, quantity,
		purchasedPrice, sellingPrice, rating, edition, frontImg, backImg, extraImg)
		VALUES ('$title', '$category','$country','$language', '$pubDate', '$publisher', '$mediaType','$desc', '$qty', '$purPrice',
			'$sellPrice', '$rating', '$edition', '$frontImg', '$backImg', '$extraImg')";
			$result = mysqli_query($conn, $sql);
			if($result){
				$last_id = mysqli_insert_id($conn);
				$sql1 = "INSERT INTO BookAuthor (bookID, authorID)
				VALUES ('$last_id', '$author')";
				$result1 = mysqli_query($conn, $sql1);
				if ($result1) {
					echo "<script>window.alert('Book added successfully!')</script>";
				} else {
					echo "Fail adding author!Error: " . mysqli_error($conn);
				}
				//echo "<script>window.location=('registerbook.php')</script>";
			} else {
				$_SESSION['message']='Fail adding book!';
				echo "Fail adding Book!Error: " . mysqli_error($conn);
			}
		}
		?>
		<!DOCTYPE html>
		<html>
		<head>
			<title>Register Book</title>
		</head>
		<body>
			<form class="form-horizontal" action="" method="post" enctype="multipart/form-data">
				<div class="col-md-6" style="padding-left: 100px">
					<div class="form-group">
						<label class="col-sm-4 control-label">Title</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" name="txtTitle" placeholder="War and Peace" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Description</label>
						<div class="col-sm-5">
							<textarea name="txtDesc" class="form-control" rows="3" placeholder="Overview of the book..."></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Published Date</label>
						<div class="col-sm-5">
							<input type="text" id="datepicker" class="form-control" value="<?php echo date('Y-m-d');?>" name="txtPublishedDate" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Publisher</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" name="txtPublisher" placeholder="Light House" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Quantity</label>
						<div class="col-sm-5">
							<input type="number" class="form-control" name="txtQty" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Purchased Price</label>
						<div class="col-sm-5">
							<input type="number" class="form-control" name="txtPurchasedPrice" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Selling Price</label>
						<div class="col-sm-5">
							<input type="number" class="form-control" name="txtSellingPrice" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Edition</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" name="txtEdition" placeholder="First Edition" />
						</div>
					</div>

				</div>
				<div class="col-md-6">
					<div class="form-group">
						<label class="col-sm-2 control-label">Author</label>
						<div class="col-sm-5">
							<select class="form-control" name="txtAuthor">
								<?php
								$sql = "select * from Author";
								$result = mysqli_query($conn, $sql);

								if (mysqli_num_rows($result) > 0) {
									while($row = mysqli_fetch_assoc($result)) {
										echo "<option value=" . $row['authorID'] . ">" . $row["authorName"] . "</option>";
									}
								} else {
									echo "<option value='0'>-</option>";
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="sel1" class="col-sm-2 control-label">Category</label>
						<div class="col-sm-5">
							<select class="form-control" name="txtCategory">
								<?php
								$sqlCategory = "select * from Category";
								$resultCategory = mysqli_query($conn, $sqlCategory);

								if (mysqli_num_rows($resultCategory) > 0) {
									while($rowCategory = mysqli_fetch_assoc($resultCategory)) {
										echo "<option value=" . $rowCategory['categoryID'] . ">" . $rowCategory["categoryName"] . "</option>";
									}
								} else {
									echo "<option value='0'>-</option>";
								}
								?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="inputPassword3" class="col-sm-2 control-label">Country</label>
						<div class="col-sm-5">
							<select class="form-control" name="txtCountry">
								<option value="UK">UK</option>
								<option value="US">US</option>
								<option value="Myanmar">Myanmar</option>
								<option value="Korea">Korea</option>
								<option value="Japan">Japan</option>
								<option value="China">China</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="inputPassword3" class="col-sm-2 control-label">Language</label>
						<div class="col-sm-5">
							<select class="form-control" name="txtLanguage">
								<option value="English">English</option>
								<option value="Myanmar">Myanmar</option>
								<option value="Chinese">Chinese</option>
								<option value="Korean">Korean</option>
								<option value="Japanese">Japanese</option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="inputPassword3" class="col-sm-2 control-label">Media Type</label>
						<div class="col-sm-5">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="txtPrinted" value="printed" checked> Printed
								</label>
								<label style="padding-left:40px">
									<input type="checkbox" name="txtEbook" value="ebook"> ebook
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="inputPassword3" class="col-sm-2 control-label">Front Image</label>
						<div class="col-sm-5">
							<input type="file" class="form-control" name="txtImg[]"/>
						</div>
					</div>
					<div class="form-group">
						<label for="inputPassword3" class="col-sm-2 control-label">Back Image</label>
						<div class="col-sm-5">
							<input type="file" class="form-control" name="txtImg[]"/>
						</div>
					</div>
					<div class="form-group">
						<label for="inputPassword3" class="col-sm-2 control-label">Extra Image</label>
						<div class="col-sm-5">
							<input type="file" class="form-control" name="txtImg[]"/>
						</div>
					</div>
					<br>
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-5">
							<input type="submit" class="btn btn-primary" name="btnsubmit" value="Submit"/>
							<input type="reset" class="btn btn-primary" value="Clear"/>
						</div>
					</div>
				</div>
				<div><?php echo $_SESSION['message'] ?></div>
			</form>
		</body>
		</html>
