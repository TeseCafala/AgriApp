<?php
// logout.php
session_start();
session_destroy();
header("Location: ../login.php"); // ou ajuste se o arquivo de login tiver outro nome
exit();
?>
