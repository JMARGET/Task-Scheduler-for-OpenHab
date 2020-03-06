<?php
    require 'global.php';
    require './classes/storage.php';


//SINCE THIS IS THE DEFAULT PAGE MAKE SURE IT REDIRECTS CORRECTLY TO Default.php
$strSelf=$_SERVER['PHP_SELF'];
$strURI=$_SERVER['REQUEST_URI'];
$pos=strpos(strtolower($strURI),strtolower($strSelf));

if (is_bool($pos)){
    header('Location: /Default.php');
    exit;
}

?>

<html>

<head>
    <title>OpenHab Task Scheduler</title>
    <?php include 'BootstrapDefine.php';?>
</head>

<body>
    <!-- Required to display the menu on each and every page -->
    <?php include 'Menu.php';?>

    <!-- Display the add new task icon -->
    <?php include 'tasknew.php';?>
    <!-- Expose the list opf all triggers -->
    <?php include 'tasklist.php';?>

<!-- Default -->
<span class="iconic" data-glyph="database" title="database" aria-hidden="true"></span>
<!-- Bootstrap -->
<span class="iconic iconic-database" title="database" aria-hidden="true"></span>
<!-- Foundation -->
<span class="fi-database" title="database" aria-hidden="true"></span>


</body>

</html>