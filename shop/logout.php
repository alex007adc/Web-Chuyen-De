<?php
session_start();
session_destroy();
header("Location: shop/index.php");
exit();
?>
