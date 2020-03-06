<?php 
    //THOSE ARE REQUIRED SO THAT THE PAGE IS COMPLIANT WITH THE SUBCLASSES
    require 'global.php';
    require './classes/storage.php';

    $tasksMngr=StorageManager::LoadTasks();
    $ohInfoMngr=StorageManager::LoadOHInfo();
    
    $taskItem =  $tasksMngr->GetItem($_GET['idtask']);

    //TASK RELATED FUNCTIONS//////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////
    
    //ADDS A NEW TASK TO THE COLLECTION
    if (isset($_POST["cmdTaskExistingSave"])){

        //CREATE A NEW TASK INSTANCE AND ASSIGN PROPERTIES
        $taskItem->Name=$_POST["name"] ?? null;
        $taskItem->IsEnabled=isset($_POST["enabled"]) ? true : false;
        $taskItem->Comment=$_POST["comment"] ?? null;
        $taskItem->Days=$_POST["days"] ?? array();

        //ADD TON COLLECTION + SAVE TO DISK

        StorageManager::Save($tasksMngr);

    }

    //CALLED WHEN AN ACTION STATUS MUST BE TOGGLED
    if (isset($_POST["cmdTaskToggle"])){

        $taskItem->IsEnabled = !$taskItem->IsEnabled;

        StorageManager::Save($tasksMngr);
    
    }
    
    //ACTION RELATED FUNCTIONS///////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////

    //ADDS A NEW ACTION TO THE COLLECTION
    if (isset($_POST["cmdActionNewSave"])){
       
        $Action;

        //THE ACTION TYPE
        if(isset($_POST["Action"])){
          
            switch ($_POST["Action"]){
                case 0: $Action=Action::CreateNew(0,$_POST["rollerdevices"] ?? null);
                break;
                case 1: $Action=Action::CreateNew(1,$_POST["rollerdevices"] ?? null);
                break;
                case 2: $Action=Action::CreateNew(2,$_POST["lightdevices"] ?? null);
                break;
                case 3: $Action=Action::CreateNew(3,$_POST["lightdevices"] ?? null);
                break;
                case 4: 
                    $Action=Action::CreateNew(4,$_POST["setvaluedevices"] ?? null);
                    $Action->Value=$_POST["setdevicevalue"] ;
                break;
            }

            $Action->Name=$_POST["actionname"];
        }

      


        //THE TRIGGER TYPE
        //THE TRIGGER IS BASED ON THE DROPDOWN VALUE
        switch ($_POST["triggerType"]) {
          case 0:
              $TimeBase = new TimeBasedTrigger();
              $TimeBase->Hour=$_POST["hour"];
              $TimeBase->Minute=$_POST["minute"];
              $Action->Trigger = $TimeBase;
              break;
          case 1:
              $SunriseBase = new SunriseBasedTrigger();
              $SunriseBase->OffsetHour=$_POST["offsethour"];
              $SunriseBase->OffsetMinute=$_POST["offsetminute"];
              $SunriseBase->OffsetDirection=$_POST["offsetdirection"];
              $Action->Trigger = $SunriseBase;
              break;
          case 2:
              $SunsetBase = new SunsetBasedTrigger();
              $SunsetBase->OffsetHour=$_POST["offsethour"];
              $SunsetBase->OffsetMinute=$_POST["offsetminute"];
              $SunsetBase->OffsetDirection=$_POST["offsetdirection"];
              $Action->Trigger = $SunsetBase;
              break;
          }

          

        //ADD THE LINKS BASED ON DEVICES NAMES
        if (isset($Action->DeviceNames)){
            $Action->DeviceLinks=$ohInfoMngr->GetLinksForDevices($Action->DeviceNames);
        }

        $taskItem->AddAction($Action);
        //ADD TON COLLECTION + SAVE TO DISK

        StorageManager::Save($tasksMngr);


    }

    //UPDATES AN ACTIONITEM
    if (isset($_POST["cmdActionExistingSave"])){
               
        $Action=$taskItem->GetAction($_POST["itemid"]);

        //THE ACTION TYPE
        if(isset($_POST["Action"])){
          
            switch ($_POST["Action"]){
                case 0: $Action=Action::CreateNew(0,$_POST["rollerdevices"] ?? null);
                break;
                case 1: $Action=Action::CreateNew(1,$_POST["rollerdevices"] ?? null);
                break;
                case 2: $Action=Action::CreateNew(2,$_POST["lightdevices"] ?? null);
                break;
                case 3: $Action=Action::CreateNew(3,$_POST["lightdevices"] ?? null);
                break;
                case 4: 
                    $Action=Action::CreateNew(4,$_POST["setvaluedevices"] ?? null);
                    $Action->Value=$_POST["setdevicevalue"] ;
                break;
            }

            $Action->Name=$_POST["actionname"];
        }

      


        //THE TRIGGER TYPE
        //THE TRIGGER IS BASED ON THE DROPDOWN VALUE
        switch ($_POST["triggerType"]) {
          case 0:
              $TimeBase = new TimeBasedTrigger();
              $TimeBase->Hour=$_POST["hour"];
              $TimeBase->Minute=$_POST["minute"];
              $Action->Trigger = $TimeBase;
              break;
          case 1:
              $SunriseBase = new SunriseBasedTrigger();
              $SunriseBase->OffsetHour=$_POST["offsethour"];
              $SunriseBase->OffsetMinute=$_POST["offsetminute"];
              $SunriseBase->OffsetDirection=$_POST["offsetdirection"];
              $Action->Trigger = $SunriseBase;
              break;
          case 2:
              $SunsetBase = new SunsetBasedTrigger();
              $SunsetBase->OffsetHour=$_POST["offsethour"];
              $SunsetBase->OffsetMinute=$_POST["offsetminute"];
              $SunsetBase->OffsetDirection=$_POST["offsetdirection"];
              $Action->Trigger = $SunsetBase;
              break;
          }

          

        //ADD THE LINKS BASED ON DEVICES NAMES
        if (isset($Action->DeviceNames)){
            $Action->DeviceLinks=$ohInfoMngr->GetLinksForDevices($Action->DeviceNames);
        }

        $taskItem->ReplaceAction($Action,$_POST["itemid"]);

        //SAVE TO DISK
        StorageManager::Save($tasksMngr);


    }

    //CALLED WHEN AN ACTION MUST BE DUPLICATED
    if (isset($_POST["cmdActionDuplicate"])){
        $actionid=$_POST["itemid"];

        $taskItem->DuplicateAction($actionid);

        StorageManager::Save($tasksMngr);StorageManager::Save($tasksMngr);

    }

    //CALLED WHEN AN ACTION MUST BE DELETED
    if (isset($_POST["cmdActionDelete"])){
        $actionid=$_POST["itemid"];

        $taskItem->RemoveAction($actionid);

        StorageManager::Save($tasksMngr);

    }

    //CALLED WHEN AN ACTION STATUS MUST BE TOGGLED
    if (isset($_POST["cmdActionToggle"])){
        $actionid=$_POST["itemid"];
    
        $action = $taskItem->GetAction($actionid);

        $action->IsEnabled= !$action->IsEnabled;
    
        StorageManager::Save($tasksMngr);
    
    }

    //CALLED WHEN  ACTION TEST IS CLICKED
    if (isset($_POST["cmdActionTest"])){
        $actionid=$_POST["itemid"];

        $action = $taskItem->GetAction($actionid);

        $action->RunAction();

    }


?>




<html>

<header>
    <?php include 'BootstrapDefine.php';?>
    <script type="text/javascript" src="js/jsScripts.js"></script>
</header>

<body>

    <!-- Required to display the menu on each and every page -->
    <?php include 'Menu.php';?>

    <div class="container">
        <?php include 'taskedit.php';?>


        <div class="row">
            <?php foreach($taskItem->Actions as $actionItem): ?>

            <div class="col-sm-6 mb-2">
                <?php include 'taskactionedit.php';?>
            </div>

            <?php endforeach; ?>

        </div>
    </div>



</body>

</html>