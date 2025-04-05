<?php
session_start();
session_destroy(); // Remove session
header("Location: login.php");
exit;
