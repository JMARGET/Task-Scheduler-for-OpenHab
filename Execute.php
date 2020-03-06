<?php

require 'global.php';
require './classes/storage.php';

// http://localhost:100/Execute.php?RunFor=15:30&SetSunrise=8:30&SetSunset=21:15

//$tmeInfo= TimeInfo::LoadFromLocalFile();
$tmeInfo= StorageManager::LoadTimeInfo();

if (isset($_GET['setsunrise'])){
   $tmeInfo->Sunrise=$_GET['setsunrise'];
   StorageManager::Save($tmeInfo);
   echo "Sunrise defined at " .  $tmeInfo->GetFormattedSunrise() ;
}

if (isset($_GET['setsunset'])){
    $tmeInfo->Sunset=$_GET['setsunset'];
    StorageManager::Save($tmeInfo);
    echo "Sunset defined at " .  $tmeInfo->GetFormattedSunset();
}


if (isset($_GET['runfor'])){
    
    $TaskMngr=StorageManager::LoadTasks();
    $TaskRunNames=array();

    $timeNow=strtotime($_GET['runfor']);

    $Exec = $TaskMngr->GetActionsToRun($timeNow);

    if (count($Exec)==0){
        echo "No Task to run.";
    }else{
        foreach($Exec  as $e){
            array_push( $TaskRunNames,$e->Name);
            $e->RunAction();
        }
        echo "Executed: " . implode(",",$TaskRunNames);
    }
}


 
?>