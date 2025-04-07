<?php
start_session();      //bug
destroy_session();
header("Location: header.php");  
exit();
?>
