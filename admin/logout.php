<?php

ob_start();

session_start();

unset($_SESSION["admin"]);
unset($_SESSION["admin_session_id"]);

header("Location: index.php");

exit();

ob_end_flush();
