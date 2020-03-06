<?php

//THOSE ARE REQUIRED SO THAT THE PAGE IS COMPLIANT WITH THE SUBCLASSES
require 'ObjectDefinitions.php';
require 'GlobalFunctions.php';


$OHItemMngr;

try {
    $OHItemMngr = OHItemManager::LoadFromLocalFile();

}catch(Exception $e){

    //CASE TO BE HANDLED HERE
    $OHItemMngr = OHItemManager::LoadFromOpenHab();
    $OHItemMngr->Save();
}

if (IsPostBack()){

    try{

        //LOAD THE DATABASE FIRST
        $Storage=TaskStorage::LoadTaskStorage();

        //WORK THROUGH A NEW TASK WITH THE POST PARAMETERS (ASSUMED VALID BECAUSE OF JAVASCRIPT CHECK)
        $DailyTaskItem = new DailyTask;
        $DailyTaskItem->Name=$_POST["name"];
        $DailyTaskItem->Comment=$_POST["comment"];
        $DailyTaskItem->Days=!empty($_POST["days"]) ? $_POST["days"]: array();
        $DailyTaskItem->IsEnabled=isset($_POST["enabled"]) ? true : false;

        //THE TRIGGER IS BASED ON THE DROPDOWN VALUE
        switch ($_POST["triggerType"]) {
            case 0:
                $TimeBase = new TimeBasedTrigger();
                $TimeBase->Hour=$_POST["hour"];
                $TimeBase->Minute=$_POST["minute"];
                $DailyTaskItem->Trigger = $TimeBase;
                break;
            case 1:
                $SunriseBase = new SunriseBasedTrigger();
                $SunriseBase->OffsetHour=$_POST["offsethour"];
                $SunriseBase->OffsetMinute=$_POST["offsetminute"];
                $SunriseBase->OffsetDirection=$_POST["offsetdirection"];
                $DailyTaskItem->Trigger = $SunriseBase;
                break;
            case 2:
                $SunsetBase = new SunsetBasedTrigger();
                $SunsetBase->OffsetHour=$_POST["offsethour"];
                $SunsetBase->OffsetMinute=$_POST["offsetminute"];
                $SunsetBase->OffsetDirection=$_POST["offsetdirection"];
                $DailyTaskItem->Trigger = $SunsetBase;
                break;
        }

        //THE ACTION TYPE
        if(isset($_POST["Action"])){

            if ($_POST["Action"]==0){
                $DailyTaskItem->Action=new ActionOPEN($_POST["rollerdevices"] ?? null);
            }

            if ($_POST["Action"]==1){
                $DailyTaskItem->Action=new ActionCLOSE($_POST["rollerdevices"] ?? null);
            }

            if ($_POST["Action"]==2){
                $DailyTaskItem->Action=new ActionSWITCHON($_POST["lightdevices"] ?? null);
            }

            if ($_POST["Action"]==3){

                $DailyTaskItem->Action=new ActionSWITCHOFF($_POST["lightdevices"] ?? null);
            }

            if ($_POST["Action"]==4){
                $DailyTaskItem->Action=new ActionSETVALUE($_POST["setvaluedevices"] ?? null,$_POST["setdevicevalue"]);
            }
        }


        //ADD THE LINKS BASED ON DEVICES NAMES
        if (isset($DailyTaskItem->Action->DeviceNames)){
            $DailyTaskItem->Action->DeviceLinks=$OHItemMngr->GetLinksForDevices($DailyTaskItem->Action->DeviceNames);
        }



        //ADD THE ITEM TO THE COLLECTION + SAVE TO DISK
        $Storage->Add ($DailyTaskItem);
        $Storage->Save ();
    
        header("Location: Default.php");

    }catch(Exception $e){
        //CASE TO BE HANDLED HERE
    }

} else {


        
    
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

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" id="submissionform"
        onsubmit="return TRIGGERFORM.ValidateTrigger ()">


        <div class="mx-auto px-5">

            <div class="form-group row">
                <label for="name" class="col-sm-1 col-form-label">Name</label>
                <div class="col-sm">
                    <input type="text" class="form-control" id="name" name="name" placeholder="Task Name"
                        aria-describedby="NameHelp">
                </div>
            </div>

            <div class="form-group row">

                <label for="Trigger" class="col-sm-1 col-form-label">Trigger</label>

                <div class="col-sm">
                    <select name="triggerType" class="form-control" id="triggerType" onchange="TRIGGERFORM.ToogleTriggerProperties();">
                        <?php 
                            $i=0;
                                foreach( trigger::GetTriggers() as $Value){
                                    echo ('<option class="form-control" value="' . $i . '">' . $Value . '</option>');
                                    $i++;
                                }
                            unset($i);
                        ?>
                    </select>
                </div>

                <!-- CASE THIS IS A TIME BASED EVENT -->
                <div id="Time" class="col-sm">
                    <div class="input-group">
                        <input type="text" class="form-control" name="hour" placeholder="Hours">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">H</span>
                        </div>
                        <input type="text" class="form-control" name="minute" placeholder="Minutes">
                    </div>
                </div>

                <!-- CASE THIS IS A SUNRISE OR SUNSET BASED EVENT -->
                <div id="SunriseSunset" class="col-sm">
                    <div class="input-group ">
                        <div class="input-group-prepend">
                            <select name="offsetdirection" class="form-control input-group-text">
                                <option value="+">+</option>
                                <option value="-">-</option>
                            </select>
                        </div>
                        <input type="text" class="form-control" name="offsethour" placeholder="Hours">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">H</span>
                        </div>
                        <input type="text" class="form-control" name="offsetminute" placeholder="Minutes">
                    </div>

                    <!-- Hide this group by default -->
                    <script>
                    $('#SunriseSunset').hide();
                    </script>

                </div>



            </div>

            <div class="form-group row">
                <label for="name" class="col-sm-1 col-form-label">Days</label>
                <div class="col-sm-11">
                    <div class="form-group">

                        <?php 
                            $DayList=[1=>"Monday",2=>"Tuesday",3=>"Wednesday",4=>"Thursday",5=>"Friday",6=>"Saturday",7=>"Sunday"];

                            foreach( $DayList as $key => $value){
                                echo('<div class="form-check form-check-inline">');
                                echo('<input type="checkbox" name="days[]" value="' . $key . '" />');
                                echo ('<label class="form-check-label" for="inlineCheckbox1">' . $value . '</label>');
                                echo ('</div>');
                            }
                 
                        ?>

                    </div>
                </div>
            </div>

            <div class="form-group row">
                <label for="Action" class="col-sm-1 col-form-label">Action</label>
                <div class="col-sm">
                    <select name="Action" class="form-control input-group-text" id="actionTypes" onchange="TRIGGERFORM.ToogleDevices();">
                        <option class="form-control" >-</option>'
                        <?php 
                        $i=0;
                            foreach( Action::GetActionTypes() as $Value){
                                echo ('<option class="form-control" value="' . $i . '">' . $Value . '</option>');
                                $i++;
                            }
                        unset($i);
                    ?>
                    </select>
                </div>
                <div class="col-sm">
                    <input type="text" id="setdevicevalue" class="form-control" name="setdevicevalue" placeholder="Value (%)">
                
                    <script>
                        $('#setdevicevalue').hide();
                    </script>
                
                </div>
            </div>


            <div class="form-group row" id="device_lights">
                <label for="Action" class="col-sm-1 col-form-label">Devices</label>
                <div class="col-sm-11">
                <?php
                        InputNewDevice($OHItemMngr->GetLights(),"lightdevices");
                ?>
                </div>

                <!-- Hide this group by default -->
                <script>
                $('#device_lights').hide();
                </script>

            </div>

            <div class="form-group row" id="device_rollers">
                <label for="Action" class="col-sm-1 col-form-label">Devices</label>
                <div class="col-sm-11">
                <?php
                        InputNewDevice( $OHItemMngr->GetRollers(),"rollerdevices");
                ?>
                </div>
                <!-- Hide this group by default -->
                <script>
                $('#device_rollers').hide();
                </script>
            </div>

            <div class="form-group row" id="device_setvalue">
                <label for="Action" class="col-sm-1 col-form-label">Devices</label>
                <div class="col-sm-11">
                <?php
                        InputNewDevice( $OHItemMngr->GetSetValues(),"setvaluedevices");
                    ?>
                </div>
                <!-- Hide this group by default -->
                <script>
                $('#device_setvalue').hide();
                </script>
            </div>





            <div class="form-group row">
                <label for="comment" class="col-sm-1 col-form-label">Comments</label>
                <div class="col-sm-11">
                    <textarea name="comment" rows="3" class="form-control"></textarea>
                </div>
            </div>

            <div class="form-group row">
                <label for="comment" class="col-sm-1 col-form-label"></label>
                <div class="col-sm-5">
                    <input type="checkbox" class="" name="enabled" value="1" checked="checked" />
                    <label class="form-check-label" for="exampleCheck1">Enable task</label>
                </div>
                <div class="col-sm-6 text-right">
                    <input class="btn btn-primary " type="submit" name="cmdSubmit" value="Save">
                </div>
            </div>

        </div>



        </div>
    </form>



</body>

</html>

<?php 
            


    function InputNewDevice ($OHDevices,$PostGroupName){
        $DeviceColCount=3;
        $i=0;

        if(isset($OHDevices)){
            foreach($OHDevices as $e){
                if ($i==0){echo '<div class="row">';}
                echo '<div class="col-sm">';
                echo '<div class="col-sm form-check form-check-inline">';
                echo '<input type="checkbox" name="' . $PostGroupName . '[]" value="' . $e->Name . '"/>';
                //echo '<input type="hidden" name="toto[]" value="' . $e->Link . '" />';
                // echo '<label class="form-check-label" for="inlineCheckbox2">'. $e->Label. '(' . $e->Name . ')' . '</label>';
                echo '<label class="form-check-label" for="inlineCheckbox2">'. $e->Label .  '</label>';
                echo '</div>';
                echo '</div>';
                $i++;

                if ($i==$DeviceColCount){echo '</div>';$i=0;}
            }
            
            if(!$i==0){
                while ($i < $DeviceColCount) {
                    $i++;
                    echo '<div class="col-sm">';
                    echo '<div class="col-sm form-check form-check-inline">';
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';
            }

        }



        
    }
    
    

    
    ?>