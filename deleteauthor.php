<?php
include('connection.php');

$authorID=$_REQUEST['authorID'];

$sql = "DELETE FROM Author WHERE authorID=$authorID";

if (mysqli_query($conn, $sql)) {
    echo "<script>window.alert('Author Successfully Deleted.')</script>";
		echo "<script>window.location='registerauthor.php'</script>";
} else {
    echo "Error deleting record: " . mysqli_error($conn);
}
?>
