<?php
#logout.php
session_start();
setcookie("cook", "", time() - 60, "/", "", 0);
unset($_SESSION['userName']);
unset($_SESSION['userId']);
$_SESSION = array();

session_destroy();
?>
<script language="javascript">
  window.location.href = "./login.php";
</script>