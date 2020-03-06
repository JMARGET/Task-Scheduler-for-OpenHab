<?php
session_start();

//THOSE ARE REQUIRED SO THAT THE PAGE IS COMPLIANT WITH THE SUBCLASSES
require 'global.php';
require 'classes\storage.php';

$DayList=[1=>"Monday",2=>"Tuesday",3=>"Wednesday",4=>"Thursday",5=>"Friday",6=>"Saturday",7=>"Sunday"];
$ohInfoMngr=StorageManager::LoadOHInfo(); 


//IMPORTANT TO GET THESESSION CONTENT TO LOAD PROPERTIES
if (isset($_SESSION["taskitem"])){
    $TaskItem=unserialize($_SESSION["taskitem"]);

    if($TaskItem==false){
        $TaskItem=new Task();
    }

}else{
    $TaskItem=new Task();

}

 
if (IsPostBack()){

    if (isset($_POST["cmdsave"])){
   
        //WORK THROUGH A NEW TASK WITH THE POST PARAMETERS (ASSUMED VALID BECAUSE OF JAVASCRIPT CHECK)
        $TaskItem->Name=$_POST["name"];
        $TaskItem->Days=!empty($_POST["days"]) ? $_POST["days"]: array();
        $TaskItem->Comment=$_POST["comment"];
        $TaskItem->IsEnabled=isset($_POST["enabled"]) ? true : false;
        
        $_SESSION["taskitem"]=serialize($TaskItem);

    }

    if (isset($_POST["cmdreset"])){
        unset( $_SESSION["taskitem"]);
        $TaskItem=new Task();
    }


    if (isset($_POST["cmdtempsave"])){
        $TaskItem->Name=$_POST["name"];
        $TaskItem->Days=!empty($_POST["days"]) ? $_POST["days"]: array();
        $TaskItem->Comment=$_POST["comment"];
        $TaskItem->IsEnabled=isset($_POST["enabled"]) ? true : false;

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

        $TaskItem->AddAction($Action);

        $_SESSION["taskitem"]=serialize($TaskItem);

    }

    //CALLED WHEN AN ACTION MUST BE DUPLICATED
    if (isset($_POST["cmdactionduplicate"])){
     $actionid=$_POST["itemid"];

     $TaskItem->DuplicateAction($actionid);

     $_SESSION["taskitem"]=serialize($TaskItem);

    }

    //CALLED WHEN AN ACTION MUST BE DELETED
    if (isset($_POST["cmdactiondelete"])){
      $actionid=$_POST["itemid"];
 
      $TaskItem->RemoveAction($actionid);
 
      $_SESSION["taskitem"]=serialize($TaskItem);
 
    }

    //CALLED WHEN AN ACTION STATUS MUST BE TOGGLED
    if (isset($_POST["cmdactiontoggle"])){
      $actionid=$_POST["itemid"];
  
      $action = $TaskItem->GetAction($actionid);

      $action->IsEnabled= $action->IsEnabled ? false : true;
  
      $_SESSION["taskitem"]=serialize($TaskItem);
  
    }

      //CALLED WHEN  ACTION TEST IS CLICKED
      if (isset($_POST["cmdactiontest"])){
        $actionid=$_POST["itemid"];
    
        $action = $TaskItem->GetAction($actionid);
  
        $action->RunAction();
    
      }

}else{
    // unset( $_SESSION["taskitem"]);

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

    <form method="post" id="frm" onsubmit="return TASKFORM.ValidateTaskName ()">

        <div class="mx-auto px-5">

            <!-- DISPLAY THE SAVE BUTTON-->
            <div class="form-group row">
                <div class="col text-right">

                        <input class="btn btn-primary " type="submit" name="cmdreset" value="reset">
                        <input class="btn btn-primary " type="submit" name="cmdsave" value="Save">

                </div>

            </div>

            <!-- DISPLAY THE NAME OF THE TASK -->
            <div class="form-group row">
                <label for="name" class="col-sm-1 col-form-label">Name</label>
                <div class="col-sm">
                    <input type="text" class="form-control" id="name" name="name" placeholder="Task Name"
                        aria-describedby="NameHelp" value="<?php echo $TaskItem->Name;?>">
                </div>
            </div>

            <!-- DISPLAY THE DAYS WHERE THE TASK IS SUPPOSED TO RUN -->
            <div class="form-group row">
                <label for="name" class="col-sm-1 col-form-label">Days</label>
                <div class="col-sm-11">
                    <div class="form-group">

                        <?php foreach($DayList as $key => $value): ?>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" name="days[]" value="<?php echo $key  ?>"
                                <?php echo $TaskItem->ContainsDay($key) ? 'checked="checked"' : ''; ?> />
                            <label class="form-check-label" for="inlineCheckbox1"><?php echo $value; ?></label>
                        </div>
                        <?php endforeach; ?>

                    </div>
                </div>
            </div>

            <!-- DISPLAY THE FREE COMMENT USED IN THE TASK -->
            <div class="form-group row">
                <label for="comment" class="col-sm-1 col-form-label">Comments</label>
                <div class="col-sm-11">
                    <textarea name="comment" rows="3" class="form-control"><?php echo $TaskItem->Comment ?></textarea>
                </div>
            </div>

            <!-- DISPLAY THE SAVE BUTTON WITH THE ENABLE CHECKMARK -->
            <div class="form-group row">
                <label for="Enable" class="col-sm-1 col-form-label"></label>
                <div class="col-sm-5">
                    <input type="checkbox" class="" name="enabled" value="1"
                        <?php  echo $TaskItem->IsEnabled==True ? 'checked="checked"':'' ?> />
                    <label class="form-check-label" for="exampleCheck1">Enable task</label>
                </div>

            </div>

            <!-- DISPLAY THE ADD ACTION BUTTON -->
            <div class="form-group row">
                <label for="Enable" class="col-sm-1 col-form-label">Actions</label>
                <div class="col-sm-11">
                    <!-- Button trigger modal -->
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal_OLD"
                        onclick="if(TASKFORM.ValidateTaskName ()){ $('#exampleModal').modal('toggle'); };">
                        Add new Action
                    </button>
                </div>

            </div>


        </div>



        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Insert a new action</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <?php include 'ftasknewaction.php';?>

          
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="mx-auto px-5">
        <!-- DISPLAY THE ACTIONS LIST-->
        <div class="form-group row">
            <label for="Enable" class="col-sm-1 col-form-label"></label>
            <div class="col-sm-11">
                <?php include 'ftaskactionlist.php';?>
            </div>

        </div>



    </div>

    <script>



    </script>



</body>

</html>