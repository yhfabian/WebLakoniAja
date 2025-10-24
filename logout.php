<?php
session_start();
session_unset();
session_destroy();
setcookie('id_konselor', '', time() - 3600, '/');
setcookie('nama', '', time() - 3600, '/');
header("Location: index.php");
exit();
