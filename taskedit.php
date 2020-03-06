<?php
    $DayList=[1=>"Monday",2=>"Tuesday",3=>"Wednesday",4=>"Thursday",5=>"Friday",6=>"Saturday",7=>"Sunday"];

    if (!isset($taskItem)){
        $taskItem=new Task();
        
    }
 


?>


<div>

    <!-- NAME OF THE TASK + IS ENABLE BADGE-->

    <span>
        <!-- <span> -->
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#edittaskmodal"
            style="padding-left:0px;">
            <p class="lead" style="font-size:32px;"><?php echo $taskItem->Name ?></p>

        </button>


 

        <!-- </span> -->
        <span>
            <?php if ($taskItem->IsEnabled) { ?>
            <span class="badge badge-success">Enabled</span>
            <?php } else { ?>
            <span class="badge badge-warning">Disabled</span>
            <?php } ?>
        </span>

    </span>

    <div class="small text-right">
        <hr class="my-1">
        <!-- CREATED ON -->
        <div>
            Created on <?php echo $taskItem->CreatedOn->format('Y-m-d H:i:s')?>
        </div>

    </div>

    <!-- COMMENTS -->
    <p class="lead"><?php echo $taskItem->Comment ?></p>

    <div class="form-group">
        <div class="form-group row">
            <label for="inputPassword" class="col-sm-1 col-form-label">Runs</label>
            <div class="col-sm-11">
                <div class="form-control">
                    <?php echo $taskItem->RunsOnLitteral?>
                </div>
            </div>
        </div>
        <div class="form-group row" style=" margin-bottom: 0px;">
            <label for="staticEmail" class="col-sm-1 col-form-label">Actions</label>

            <div class="input-group mb-3 col-sm-11">
                <div class="form-control">
                    <?php echo count($taskItem->Actions) . ' Actions defined' ?>
                </div>
                <div class="input-group-append">
                    <?php include 'taskactionnew.php' ?>
                </div>
            </div>

        </div>

        <div class="form-group row" style=" margin-bottom: 0px;">
            <label for="staticEmail" class="col-sm-1 col-form-label">Status</label>
            <div class="col-sm-11">
                <form method="post" id="frmtasktoggle">
                    <input type="hidden" name="itemid" value="<?php echo ($taskItem->UID); ?>">
                    <div class="btn-group" role="group" aria-label="Basic example">

                        <button type="submit"
                            class="btn btn-sm btn-<?php echo  $taskItem->IsEnabled ? 'primary' : 'secondary'?>"
                            name="cmdTaskToggle">On</button>
                        <button type="submit"
                            class="btn btn-sm btn-<?php echo !$taskItem->IsEnabled ? 'primary' : 'secondary'?>"
                            name="cmdTaskToggle">Off</button>

                    </div>
                </form>
            </div>
        </div>



    </div>

    <!-- Modal -->
    <div class="modal fade" id="edittaskmodal" tabindex="-1" role="dialog" aria-labelledby="newtaskmodallabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form method="post" id="frmtask" onsubmit="return TASKFORM.ValidateTaskName ()">
                    <input type="hidden" name="idtask" value="<?php echo $taskItem->UID; ?>">
                    <!-- HEADER WITH THE TITLE -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><?php echo $taskItem->Name ?></h5>
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
                                aria-describedby="NameHelp" value="<?php echo $taskItem->Name; ?>">
                        </div>

                        <!-- DISPLAY THE DAYS WHERE THE TASK IS SUPPOSED TO RUN -->
                        <div class="form-group ">
                            <label for="days">Days</label>

                            <div class="form-group">

                                <?php foreach($DayList as $key => $value): ?>
                                <div class="form-check form-check-inline">
                                    <input type="checkbox" name="days[]" value="<?php echo $key  ?>"
                                        <?php echo $taskItem->ContainsDay($key) ? " checked " : ""; ?> />
                                    <label class="form-check-label"
                                        for="inlineCheckboxdays"><?php echo $value; ?></label>
                                </div>
                                <?php endforeach; ?>

                            </div>

                        </div>

                        <!-- DISPLAY THE FREE COMMENT USED IN THE TASK -->
                        <div class="form-group">
                            <label for="comment" class="">Comments</label>
                            <textarea name="comment" rows="3"
                                class="form-control"><?php echo $taskItem->Comment; ?></textarea>

                        </div>

                        <!-- DISPLAY THE SAVE BUTTON WITH THE ENABLE CHECKMARK -->
                        <div class="form-group">
                            <input type="checkbox" class="" name="enabled" value="1"
                                <?php echo $taskItem->IsEnabled ? " checked " : ""; ?> />
                            <label class="form-check-label" for="exampleCheck1">Enable task</label>
                        </div>

                    </div>

                    <!-- FOOTER WITH ALL BUTTON -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="cmdTaskExistingSave">Save</button>

                    </div>

                </form>
            </div>
        </div>
    </div>




</div>