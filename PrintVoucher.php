
<?php
	session_start();
	require_once('connect.php');
?>
<?php
	$file_type="msexcel";
	if(isset($_SESSION['voucher']))
		$sql=$_SESSION['voucher'];
		//$id=$_REQUEST['id'];
		$id=$_SESSION['OrderID'];

		$select="select userName,mobile,address,salesDate,salesID
				from customer c, sales s
				where c.userID=s.customerID
				and s.salesID='$id'";
		$query=mysql_query($select);
		$count=mysql_num_rows($query);
		$data=mysql_fetch_array($query);
		$CustomerName=$data['userName'];
		$OrderID=$data['salesID'];
		$OrderDate=$data['salesDate'];
		$Phone=$data['mobile'];


		$sqldetails="select * , od.quantity as SQty
						from sales o, booksales od, book p
						where o.salesID=od.salesID
						and p.bookID=od.bookID
						and o.salesID='$id'";
		$dret=mysql_query($sqldetails);
		$no=mysql_num_rows($dret);

	header("Content-Type:application/$file_type");
	header("Content-Disposition:attachment;filename=Voucher.xls");
	header("Pragma:no-cache");
	header("Expires:0");


	$table="<table border='1'>";

		$table.="<tr>
					<td colspan='3'><img src='css/images/qeqologo.png' height='150px' width='150px'/> <br/> Booklet <br/> Enterprise Limited </td>
					<td colspan='3'> No.156, Waizayandar Road,<br/> South Okkalapa Township,<br/> Yangon <br/> Phone - 092511111110
					</td>

				</tr>";
		$table.="<tr><th colspan='6'> SALE INVOICE</th></tr>";
		$table.="<tr>
					<td>CUSTOMER NAME</td>
					<td colspan='2'>$CustomerName</td>
					<td>VOUCHER NO</td>
					<td colspan='2'>$OrderID</td>
				</tr>";


		$table.="<tr>
					<td>CONTACT NUMBER</td>
					<td colspan='2'>$Phone</td>
					<td>Date of Invoice</td>
					<td colspan='2'>$OrderDate</td>
				</tr>";

		$table.="<tr></tr>";

		$table.="<tr>
					<th>PRODUCT NAME</th>
					<th>PRICE</th>
					<th>QUANTITY</th>
					<th colspan='2'>AMOUNT</th>
				</tr>";
			$sum=0;
		for($i=0;$i<$no;$i++)
		{
			$fetch=mysql_fetch_array($dret);
			$ProductName=$fetch['title'];
			$price=$fetch['sellingPrice'];
			$qty=$fetch['SQty'];
			$amount=$fetch['sellingPrice']*$fetch['SQty'];
			$sum+=$amount;
			$table.="<tr>
					<td>$ProductName</td>
					<td>$$price</td>
					<td>$qty</td>
					<td colspan='2'>$$amount</td>
				</tr>";
		}

		$table.="<tr>
					<td colspan='3'></td>
					<td>TOTAL</td>
					<td>$$sum</td>
				</tr>";


		$table.="<tr>
					<td colspan='6' rowspan='2'> Thank you $CustomerName for ordering. Welcome to visit and order again.</td>
				</tr>";

		$table.="</table>";
		echo $table;
		echo "<script>window.location='ProductsDisplay.php'</script>";
?>
