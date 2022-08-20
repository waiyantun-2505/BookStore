<?php
include('connection.php');

$bookID=$_REQUEST['bookID'];

$sql = "DELETE FROM Book WHERE bookID=$bookID";

if (mysqli_query($conn, $sql)) {
    echo "<script>window.alert('Book Successfully Deleted.')</script>";
		echo "<script>window.location='registerbook.php'</script>";
} else {
    echo "Error deleting record: " . mysqli_error($conn);
}
?>
