<?php
// Start the session
session_start();

// Show me the session!  
echo "<pre>";
print_r($_SESSION);
echo "</pre>";


unset($_SESSION['Error']);
session_destroy();

?>