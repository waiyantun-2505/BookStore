<?php
session_start();
session_destroy();
session_regenerate_id();
echo "<script>window.location='signin.php'</script>";
 ?>
