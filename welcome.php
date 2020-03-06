<?php
    require 'global.php';

    try{
        require './classes/storage.php';
    }catch(Exception $e) {
        echo $e->message;
    }
    

    //Makes sure that enough details are provided through VAR_Dump()
    ini_set('xdebug.var_display_max_depth', '10');
    ini_set('xdebug.var_display_max_children', '256');
    ini_set('xdebug.var_display_max_data', '1024');

    // $TaskMngr=StorageManager::LoadTasks();
    // $TimeMngr=StorageManager::LoadTimeInfo();
    // $OHMngr=StorageManager::LoadOHInfo();
?>

<html>
<header><title>This is title</title></header>
<body>
Hello world
</body>
</html>