<?php

require 'ObjectDefinitions.php';
require 'GlobalFunctions.php';

set_error_handler(function($errno, $errstr, $errfile, $errline ){
    // header("Location: Error.php");
    // throw new ErrorException($errstr, $errno, 0, $errfile, $errline);

    $data =  new ErrorException($errstr, $errno, 0, $errfile, $errline);

    // Start the session
    session_start();

    // Set session variables
    $_SESSION["Error"] = $data;

    header('Location: Error.php');

        exit('variables are not equal'); 
    });

    

$name=$hour=$minute=$comment=$itemID="";
$IsValidName=$IsValidMin=$IsValidHour=true;

$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";



if (!IsPostBack()){
    $itemID=$_GET['itemID'];
    $DailyTaskItem=new DailyTask();
    
    if (isset($itemID)) {
        $Storage=TaskStorage::LoadTaskStorage();
        $DailyTaskItem=$Storage->GetItem($itemID);
    
        $name=$DailyTaskItem->Name;
        $enabled=$DailyTaskItem->IsEnabled;
        $days=$DailyTaskItem->Days;
        $hour=$DailyTaskItem->Hour;
        $minute=$DailyTaskItem->Minute;
        $comment=$DailyTaskItem->Comment;

    } else {
        // Fallback behaviour goes here
    }

}else{
    $itemID=$_POST['itemID'];
    $name=$_POST["name"];
    $hour=$_POST["hour"];
    $minute=$_POST["minute"];
    $comment=$_POST["comment"];
    $days=!empty($_POST["days"]) ? $_POST["days"]: array();
    $isValid=true;
    
    $enabled =false;
    if (isset($_POST["enabled"])){$enabled=true;};
 
    if (empty($name)){$nameErr="Invalid Name"; $IsValidName=false; $isValid=false;}
 
 
    if (preg_match("/^(?:2[0-3]|[01][0-9]|[0-9])$/",$hour)===0 ){
     $hourErr="Invalid hour";
     $isValid=false;
     $IsValidHour=false;
    }
 
    if (preg_match("/^([0-5][0-9]|[0-9])$/",$minute)===0){
     $minErr="Invalid minute";
     $isValid=false;
     $IsValidMin=false;
    }
 
    if ($isValid){
 
    $Storage=TaskStorage::LoadTaskStorage();
    $DailyTaskItem=$Storage->GetItem($itemID);
     
     $DailyTaskItem->Name=$name;
     $DailyTaskItem->IsEnabled=$enabled;
     $DailyTaskItem->Days=$days;
     $DailyTaskItem->Hour=$hour;
     $DailyTaskItem->Minute=$minute;
     $DailyTaskItem->Comment=$comment;
 
     $Storage->Save ();
 
     header("Location: Default.php");

}


}






?>


<html>

<header>
    <?php include 'BootstrapDefine.php';?>
</header>

<body>

    <!-- Required to display the menu on each and every page -->
    <?php include 'menu.php';?>


    <form action="<?php echo htmlspecialchars($actual_link)?>" method="post">

        <input type="hidden" name="itemID" value="<?php echo $itemID;?>" />

        <div class="container">

            <div class="form-group w-50">
                <label for="Name">Name</label>
                <input type="text" class="form-control  <?php echo ($IsValidName) ?  '' :  'is-invalid'  ?> "
                    name="name" value="<?php echo $name;?>" placeholder="Task Name" aria-describedby="emailHelp">
                <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
            </div>

            <div class="form-group">
                <label for="Name">Time<small class="text-muted"> (24h formatting to be used)</small></label>
                <div class="input-group mb-2 w-25">
                    <input type="text" class="form-control  <?php echo ($IsValidHour) ?  '' :  'is-invalid'  ?>"
                        name="hour" value="<?php echo $hour;?>" placeholder="Hours">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="basic-addon1">H</span>
                    </div>
                    <input type="text" class="form-control <?php echo ($IsValidMin) ?  '' :  'is-invalid'  ?>"
                        name="minute" value="<?php echo $minute;?>" placeholder="Minutes">
                </div>

            </div>

            <div class="form-group">

                <!-- <label for="Name">Days: </label> -->

                <div class="form-check form-check-inline">
                    <input type="checkbox" name="days[]" value="0"
                        <?php if (in_array(0,$days)) { echo 'checked="checked"'; } ?> />
                    <label class="form-check-label" for="inlineCheckbox1">Monday</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="checkbox" name="days[]" value="1"
                        <?php if (in_array(1,$days)) { echo 'checked="checked"'; } ?> />
                    <label class="form-check-label" for="inlineCheckbox2">Tuesday</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="checkbox" name="days[]" value="2"
                        <?php if (in_array(2,$days)) { echo 'checked="checked"'; } ?> />
                    <label class="form-check-label" for="inlineCheckbox2">Wednesday</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="checkbox" name="days[]" value="3"
                        <?php if (in_array(3,$days)) { echo 'checked="checked"'; } ?> />
                    <label class="form-check-label" for="inlineCheckbox2">Thursday</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="checkbox" name="days[]" value="4"
                        <?php if (in_array(4,$days)) { echo 'checked="checked"'; } ?> />
                    <label class="form-check-label" for="inlineCheckbox2">Friday</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="checkbox" name="days[]" value="5"
                        <?php if (in_array(5,$days)) { echo 'checked="checked"'; } ?> />
                    <label class="form-check-label" for="inlineCheckbox2">Saturaday</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="checkbox" name="days[]" value="6"
                        <?php if (in_array(6,$days)) { echo 'checked="checked"'; } ?> />
                    <label class="form-check-label" for="inlineCheckbox2">Sunday</label>
                </div>
            </div>


            <div class="form-group">
                <label for="Comments">Comment</label>
                <textarea name="comment" rows="3" class="form-control"><?php echo $comment;?></textarea>
            </div>


            <div class="btn-toolbar justify-content-between" role="toolbar" aria-label="Toolbar with button groups">

                <div class="">
                    <input type="checkbox" class="" name="enabled" value="1"
                        <?php if ($enabled) { echo 'checked="checked"'; } ?> />
                    <label class="form-check-label" for="exampleCheck1">Enable task</label>
                </div>

                <div class="input-group">
                    <input class="btn btn-primary" type="submit" value="Save">
                </div>
            </div>

        </div>



        </div>
    </form>

</body>

</html>