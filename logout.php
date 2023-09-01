<?php

session_start();

unset($_SESSION["user"]);
unset($_SESSION["user_session_id"]);

header("Location: login.php");

exit();
