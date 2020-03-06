<?php
    require 'global.php';
    require './classes/storage.php';


$StartDate=$_GET["start"]?? null;
$DisplayDays=$_GET["dispdays"]?? 2;
$Storage;

date_default_timezone_set('Europe/Paris');

//FIRST MAKE SURE THE STARTDATE IS SPECIFIED. IF NOT POSTBACK WITH THE CURRENT DATE
if (!isset($StartDate)){

    header("Location:" . $_SERVER["PHP_SELF"] . '?start=' . date("Ymd"));
    exit;
    
}else{

    $DaysRange=array();
    
    for ($i = 0; $i < $DisplayDays; $i++) {
        $IncrementalDate = new DateTime($StartDate);
        $IncrementalDate->modify('+' . $i . 'day');
       array_push($DaysRange,$IncrementalDate);
    }

    $tasksMngr=StorageManager::LoadTasks();


}

if (IsPostBack()){

    if (!empty($_POST['cmdenableday'])){
        // echo $_POST['itemid'] . "\n";
        // echo date('D, M j, Y',$_POST['day']);

        $thisTask= $tasksMngr->GetTaskFromActionId($_POST['itemid']);
        $thisAction= $thisTask->GetAction($_POST['itemid']);

        $thisAction->RemoveSkipped($_POST['day']);

        StorageManager::Save($tasksMngr);

    }

    if (isset($_POST['cmddisableday'])){
        // echo $_POST['itemid'] . "\n";
        // echo date('D, M j, Y',$_POST['day']);

        $thisTask= $tasksMngr->GetTaskFromActionId($_POST['itemid']);
        $thisAction= $thisTask->GetAction($_POST['itemid']);

        $thisAction->Skip($_POST['day']);
       
        StorageManager::Save($tasksMngr);
    }



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


    <div class="mx-auto px-5">

        <!-- NAVIGATION CONTROLS -->
        <div class="row" style="padding-bottom:10px;">
            <div class="col">
                <a href="<?php echo getPreviousUrl($DaysRange,$DisplayDays) ?>"><i class="fas fa-backward"></i> Previous
                </a>
            </div>
            <div class="col-6 text-center">
                <div class="dropdown show">
                    <a class="btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false" style="background-color:#FF6600;color:white;">
                        <?php echo $DisplayDays . " days view" ?>
                    </a>

                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" href="<?php echo getDisplayUrl($StartDate,2) ?>">2 days view</a>
                        <a class="dropdown-item" href="<?php echo getDisplayUrl($StartDate,4) ?>">4 days view</a>
                        <a class="dropdown-item" href="<?php echo getDisplayUrl($StartDate,7) ?>">7 days view</a>
                        <a class="dropdown-item" href="<?php echo getDisplayUrl($StartDate,14) ?>">14 days view</a>
                    </div>
                </div>
            </div>
            <div class="col text-right">
                <a href="<?php echo getNextUrl($DaysRange,$DisplayDays) ?>">Next <i class="fas fa-forward"></i> </a>
            </div>
        </div>


        <?php 
            function getNextUrl($DaysRange,$DisplayDays){

                $LastDay=end($DaysRange);
                $newStartDate = (clone ($LastDay))->modify('+1 day');

                $params=array(
                    'start'=>$newStartDate->format('Ymd'),
                    'dispdays'=>$DisplayDays
                );
                
               return  "Calendar.php?" . http_build_query($params);

            }

            function getPreviousUrl($DaysRange,$DisplayDays){
                $newStartDate = (clone ($DaysRange[0]))->modify('-' . $DisplayDays . 'day');

                $params=array(
                    'start'=>$newStartDate->format('Ymd'),
                    'dispdays'=>$DisplayDays
                );
                
               return  "Calendar.php?" . http_build_query($params);

            }

            function getDisplayUrl($StartDate, $SelectedValue){
                $params=array(
                    'start'=>$StartDate,
                    'dispdays'=>$SelectedValue
                );
                
               return  "Calendar.php?" . http_build_query($params);
            }

        ?>


        <!-- CALENDAR TABLE     -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <?php

                        if (isset($DaysRange)){
                            foreach($DaysRange as $DayItem){
                                echo '<th scope="col">' . $DayItem->format('l') . '<br><small>' . $DayItem->format('M j, Y') . ' </small>  </th>';
                            }
                        }
                    ?>
                </tr>
            </thead>
            <tbody>
                <tr>

                    <?php if (isset($DaysRange)): ?>
                        <?php foreach($DaysRange as $DayItem): ?>

                        <td>
                            <?php foreach($tasksMngr->GetActionsOnDate($DayItem) as $ActionItem): ?>

                                <?php 
                                        // BUILDS ANY USEFUL VARIABLE
                                        $IsSkipped = $ActionItem->IsSkipped($DayItem);
                                        $FormattedTime= $ActionItem->Trigger->FormattedTime;
                                        $Task = $tasksMngr->GetTaskFromActionId($ActionItem->UID);
                                        $TaskID=$Task->UID;
                                        $ItemID=$ActionItem->UID;
                                        $TaskName = $Task->Name;
                                        $ActionName = $ActionItem->Name;

                                        //BUILDS THE DATE SO THAT COMPARISON CAN HAPPEN
                                        $NowEpoch=strtotime('now');
                                        $InstanceTime = $ActionItem->Trigger->Time;
                                        $InstanceEpoch= mktime(date('H',$InstanceTime),date('i',$InstanceTime),0,$DayItem->format('m'),$DayItem->format('d'),$DayItem->format('Y'));
                                ?>

                                <form method="post">
                                    <input type="hidden" name="itemid" value="<?php echo $ActionItem->UID ?>">
                                    <input type="hidden" name="day" value="<?php echo $DayItem->format('Ymd') ?>">


                                    <!-- CASE THIS WILL HAPPEN IN THE FUTURE -->
                                    <?php if ($NowEpoch<$InstanceEpoch): ?>

                                        <?php if ($IsSkipped): ?>
                                            <div style="padding:5px;color:white;background-color:#B7950B;" class="rounded">
                                                <button class="btn btn-light btn-sm" style="vertical-align: top" type="submit" name="cmdenableday" value="1"><i style="width:18px" class="fas fa-bell-slash"></i></button>
                                                <a class="font-weight-bold" style="color:white" href="/taskview.php?idtask=<?php echo ($TaskID); ?> "><?php echo ' ' . $FormattedTime . ' ' .  $ActionName; ?><small> (<?php echo $TaskName; ?>)</small>  </a>
                                            </div>

                                        <?php else: ?>

                                            <div style="padding:5px;color:white;background-color:#229954" class="rounded">
                                                <button class="btn btn-light btn-sm" type="submit" name="cmddisableday" value="1"><i style="width:18px" class="fas fa-bell"></i></button>
                                                <a class="font-weight-bold" style="color:white" href="/taskview.php?idtask=<?php echo ($TaskID); ?> "><?php echo ' ' . $FormattedTime . ' ' .  $ActionName; ?><small> (<?php echo $TaskName; ?>)</small></a>
                                            </div>

                                        <?php endif ?>

                                    <!-- CASE IT HAPPENED IN THE PAST -->
                                    <?php else: ?>

                                        <div style="padding:5px;color:white;background-color:Gray" class="rounded">
                                            <button class="btn btn-light btn-sm" disabled><i style="width:18px" class="<?php echo $IsSkipped ? "fas fa-bell-slash" : "fas fa-bell" ?>"></i></button>
                                            <a class="font-weight-bold" style="color:white" href="/taskview.php?idtask=<?php echo ($TaskID); ?> "><?php echo ' ' . $FormattedTime . ' ' .  $ActionName; ?><small> (<?php echo $TaskName; ?>)</small></a>
                                        </div>

                                    <?php endif ?>

                                </form>
                            <?php endforeach; ?>
                        </td>

                        <?php endforeach; ?>
                    <?php endif; ?>

                </tr>
            </tbody>
        </table>

    </div>



</body>

