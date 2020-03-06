<?php
require 'global.php';
require './classes/storage.php';
//require  'ObjectDefinitions.php';
// require  'GlobalFunctions.php';


    // $Storage =TaskStorage::LoadTaskStorage();

    // $Item = New DailyTask;

    // $Trigger=New TimeBasedTrigger();

    // $Trigger->Minute=58;
    // $Trigger->Hour=23;

    // $Item->Trigger=$Trigger;

    
    // $result= TriggerTypes::GetValues();
    // echo  TriggerTypes::GetValues()[1];
    // echo $Trigger->Time;
    

    // $Storage=TaskStorage::LoadTaskStorage();

    // foreach ($Storage->TaskStorageItems as $Item){

    //     $Item->Action->RunAction();
    //     var_dump($Item);
    // }

    // $timeNow=strtotime('15:30');

    // $Exec = $Storage->GetTasksToRun($timeNow,strtotime('16:30'),$timeNow);


    // var_dump($Exec);

    // $stringtest= '+' . 2 . 'hours'  . 0 . ' minutes';

    // $new_time = date("Y-m-d H:i:s", strtotime('+3 hours 00 minutes', $timeNow));

    // $new_time = date("Y-m-d H:i:s", strtotime($stringtest, $timeNow));

    // foreach($Storage->TaskStorageItems  as $e){
    //     //$Item=new DailyTask();
    //     $Item=$e;
    //     print ($Item->Name . ' MustExecute=' . ($Item->MustExecute($timeNow,$timeNow,$timeNow) ? "True" : "False") . "\n");

    // }
    ?>

<html>

<head>
    <title>OpenHab Task Scheduler</title>
    <?php include 'BootstrapDefine.php';?>
    <script type="text/javascript" src="js/jsScripts.js"></script>
</head>

<body>
    <!-- Required to display the menu on each and every page -->
    <?php include 'Menu.php';?>

    <!-- Expose the list opf all triggers -->
    <?php include 'atasknew.php';?>




</body>

</html>