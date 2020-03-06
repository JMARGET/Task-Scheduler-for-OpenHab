<?php 

    $DayList=[1=>"Monday",2=>"Tuesday",3=>"Wednesday",4=>"Thursday",5=>"Friday",6=>"Saturday",7=>"Sunday"];

    if (isset($_POST["cmdSaveNewTask"])){
        //LOADS THE STORAGE
        $tasksMngr=StorageManager::LoadTasks();

        //CREATE A NEW TASK INSTANCE AND ASSIGN PROPERTIES
        $newTask= new Task();
        $newTask->Name=$_POST["name"] ?? null;
        $newTask->IsEnabled=isset($_POST["enabled"]) ? true : false;
        $newTask->Comment=$_POST["comment"] ?? null;
        $newTask->Days=$_POST["days"] ?? array();

        //ADD TON COLLECTION + SAVE TO DISK
        $tasksMngr->Add($newTask);
        StorageManager::Save($tasksMngr);

        //BUILD QUERY STRING AND REDIRECT TO EDIT TASK
        $getParameters=array('idtask'=>$newTask->UID);
        header('location: /taskview.php'."?". http_build_query($getParameters));
        unset($tasksMngr,$newTask);
        exit;
    }

?>


<!-- Button trigger modal -->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm  mb-2">
            <button class="btn" type="button" style="background-color:#FF6600;color:white;font-weight:bold" data-toggle="modal" data-target="#newtaskmodal">
                <i class="fas fa-plus" style="padding-right:10px"> </i>
                New Task
            </button>
        </div>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="newtaskmodal" tabindex="-1" role="dialog" aria-labelledby="newtaskmodallabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form method="post" id="frmtask" onsubmit="return TASKFORM.ValidateTaskName ()">

                <!-- HEADER WITH THE TITLE -->
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">New task</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <!-- BODY WITH ALL CONTROLS -->
                <div class="modal-body">

                    <!-- DISPLAY THE NAME OF THE TASK -->
                    <div class="form-group ">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="taskname" name="name" placeholder="Task Name"
                            aria-describedby="NameHelp">
                    </div>

                    <!-- DISPLAY THE DAYS WHERE THE TASK IS SUPPOSED TO RUN -->
                    <div class="form-group ">
                        <label for="days">Days</label>

                        <div class="form-group">

                            <?php foreach($DayList as $key => $value): ?>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" name="days[]" value="<?php echo $key  ?>" />
                                <label class="form-check-label" for="inlineCheckboxdays"><?php echo $value; ?></label>
                            </div>
                            <?php endforeach; ?>

                        </div>

                    </div>

                    <!-- DISPLAY THE FREE COMMENT USED IN THE TASK -->
                    <div class="form-group">
                        <label for="comment" class="">Comments</label>
                        <textarea name="comment" rows="3" class="form-control"></textarea>

                    </div>

                    <!-- DISPLAY THE SAVE BUTTON WITH THE ENABLE CHECKMARK -->
                    <div class="form-group">
                        <input type="checkbox" class="" name="enabled" value="1" checked />
                        <label class="form-check-label" for="exampleCheck1">Enable task</label>
                    </div>

                </div>

                <!-- FOOTER WITH ALL BUTTON -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="cmdSaveNewTask">Save & Continue</button>

                </div>

            </form>
        </div>
    </div>
</div>