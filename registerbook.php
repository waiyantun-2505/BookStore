<?php
include('admin_navbar.php');
include('connection.php');
$_SESSION['message']='';
if(isset($_GET['mode'])){
	$bookID=$_GET['bookID'];
	$sql = "SELECT * FROM Book where bookID=$bookID";
	$result = mysqli_query($conn, $sql);
	if (mysqli_num_rows($result) > 0) {
		$row = mysqli_fetch_assoc($result);
		$title=$row['title'];
		$categoryID=$row['categoryID'];
		$country=$row['country'];
		$language=$row['language'];
		$pubDate=$row['publishedDate'];
		$publisher=$row['publisher'];
		$mediaType=$row['mediaType'];
		$desc=$row['description'];
		$qty=$row['quantity'];
		$purPrice=$row['purchasedPrice'];
		$sellPrice=$row['sellingPrice'];
		$edition=$row['edition'];
	}
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST['btnsave'])){
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
			if (isset($_POST['btnupdate'])){
				$ubookID=$_POST['bookID'];
				$utitle=mysql_real_escape_string($_POST['txtTitle']);
				$uauthor=$_POST['txtAuthor'];
				$ucategoryID=$_POST['txtCategory'];
				$ucountry=$_POST['txtCountry'];
				$ulanguage=$_POST['txtLanguage'];
				$upubDate=$_POST['txtPublishedDate'];
				$upublisher=mysql_real_escape_string($_POST['txtPublisher']);
				$uprinted = $_POST['txtPrinted'];
				if(isset($_POST['txtEbook'])){ $uebook = $_POST['txtEbook']; }else{$uebook = "";}
				//$ebook = $_POST['txtEbook'];
				$umediaType = "printed";
				if($uprinted == "printed" && $uebook == ""){
					$umediaType = "printed";
				}else if($uprinted == "" && $uebook == "ebook"){
					$umediaType = "ebook";
				}else if($uprinted == "printed" && $uebook == "ebook"){
					$umediaType = "both";
				}
				$udesc=mysql_real_escape_string($_POST['txtDesc']);
				$uqty=$_POST['txtQty'];
				$upurPrice=$_POST['txtPurchasedPrice'];
				$usellPrice=$_POST['txtSellingPrice'];
				$uedition=mysql_real_escape_string($_POST['txtEdition']);

				$sql = "UPDATE Book SET title='$utitle', categoryID='$ucategoryID', country='$ucountry', language='$ulanguage'
				, publishedDate='$upubDate', publisher='$upublisher', mediaType='$umediaType', description='$udesc', quantity='$uqty'
				, purchasedPrice='$upurPrice', sellingPrice='$usellPrice', edition='$uedition' WHERE bookID=$ubookID";

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
			<title>Register Book</title>
		</head>
		<body>
			<form class="form-horizontal" action="registerbook.php" method="post" enctype="multipart/form-data">
				<div class="col-md-6" style="padding-left: 100px">
					<input type="hidden" name="bookID" value="<?php echo $bookID ?>"/>
					<div class="form-group">
						<label class="col-sm-4 control-label">Title</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" name="txtTitle" placeholder="War and Peace" value="<?php if(isset($title)){echo $title;}?>"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Description</label>
						<div class="col-sm-5">
							<textarea name="txtDesc" class="form-control" rows="3" placeholder="Overview of the book..."><?php if(isset($desc)){echo $desc;}?></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Published Date</label>
						<div class="col-sm-5">
							<input type="text" id="datepicker" class="form-control" name="txtPublishedDate" value="<?php if(isset($pubDate)){echo $pubDate;}?>"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Publisher</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" name="txtPublisher" placeholder="Light House" value="<?php if(isset($publisher)){echo $publisher;}?>"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Quantity</label>
						<div class="col-sm-5">
							<input type="number" class="form-control" name="txtQty" value="<?php if(isset($qty)){echo $qty;}?>"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Purchased Price</label>
						<div class="col-sm-5">
							<input type="number" class="form-control" name="txtPurchasedPrice" value="<?php if(isset($purPrice)){echo $purPrice;}?>"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Selling Price</label>
						<div class="col-sm-5">
							<input type="number" class="form-control" name="txtSellingPrice" value="<?php if(isset($sellPrice)){echo $sellPrice;}?>"/>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label">Edition</label>
						<div class="col-sm-5">
							<input type="text" class="form-control" name="txtEdition" placeholder="First Edition" value="<?php if(isset($edition)){echo $edition;}?>"/>
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
										$selected = $row['authorID'] == $authorID ? "selected = 'selected'" : '';
										echo "<option value=" . $row['authorID'] . " $selected >" . $row["authorName"] . "</option>";
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
										$selected = $rowCategory['categoryID'] == $categoryID ? "selected = 'selected'" : '';
										echo "<option value=" . $rowCategory['categoryID'] . " $selected >" . $rowCategory["categoryName"] . "</option>";
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
							<div style="color:red"><?php echo $_SESSION['message'] ?></div>
						</div>
					</div>
				</div>
			</form>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Title</th>
						<th>Author</th>
						<th>Category</th>
						<th>Country</th>
						<th>Language</th>
						<th>Published Date</th>
						<th>Publisher</th>
						<th>Media Type</th>
						<th>Quantity</th>
						<th>Purchased Price</th>
						<th>Selling Price</th>
						<th>Edition</th>
						<th>Action</th>
					</tr>
					<?php
					$sql = "SELECT * FROM Book";
					$result = mysqli_query($conn, $sql);

					if (mysqli_num_rows($result) > 0) {
						while($row = mysqli_fetch_assoc($result)) {
							$bookID = $row["bookID"];
							$catID = $row["categoryID"];
							$sql1 = "select authorName from Author a inner join BookAuthor ba on a.authorID=ba.authorID
							inner join Book b on b.bookID=ba.bookID where b.bookID=$bookID";
							$result1 = mysqli_query($conn, $sql1);

							if (mysqli_num_rows($result1) > 0) {
								$row1 = mysqli_fetch_assoc($result1);
								$authorID = $row1["authorName"];
							}

							$sql2 = "SELECT categoryName FROM Category where categoryID=$catID";
							$result2 = mysqli_query($conn, $sql2);

							if (mysqli_num_rows($result2) > 0) {
								$row2 = mysqli_fetch_assoc($result2);
									$catName = $row2['categoryName'];
							}

							echo "<tr>";
							echo "<td>" . $row["title"]. "</td>";
							echo "<td>" . $authorID . "</td>";
							echo "<td>" . $catName. "</td>";
							echo "<td>" . $row["country"]. "</td>";
							echo "<td>" . $row["language"]. "</td>";
							echo "<td>" . $row["publishedDate"]. "</td>";
							echo "<td>" . $row["publisher"]. "</td>";
							echo "<td>" . $row["mediaType"]. "</td>";
							echo "<td>" . $row["quantity"]. "</td>";
							echo "<td>" . $row["purchasedPrice"]. "</td>";
							echo "<td>" . $row["sellingPrice"]. "</td>";
							echo "<td>" . $row["edition"]. "</td>";
							echo "<td><a href='registerbook.php?bookID=$bookID&mode=update'>Edit</a> |
							<a href='deletebook.php?bookID=$bookID'>Delete</a> </td>";
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
