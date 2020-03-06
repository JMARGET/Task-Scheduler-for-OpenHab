<div class="row">

    <!-- DISPLAY AN ITEM FOR EACH MEMBER OF THE COLLECTION -->
    <?php foreach($TaskItem->Actions as $actionItem): ?>

    <div class="col-sm-6 mb-2">
        <form action="" method="post" style="margin:2px;" id="frm_<?php echo ($actionItem->UID); ?>">
            <div class="card">
                <div class="card-header" style="padding:6px;">
                    <div>

                        <!-- BUTTON TO DISABLE THE ACTION -->
                        <span>
                            <input type="hidden" name="itemid" value="<?php echo ($actionItem->UID); ?>">
                            <button class="btn btn-light" type="submit" name="cmdactiontoggle" value="off"><i
                                    class="fas <?php echo ($actionItem->IsEnabled) ?  'fa-toggle-on' :  'fa-toggle-off'  ?>"></i>
                            </button>
                        </span>

                        <!-- BUTTON TO TRIGGER THE MODAL FORM -->
                        <span>
                            <button type="button" class="btn btn-link font-weight-bold" data-toggle="modal"
                                data-target="#Modal_<?php echo ($actionItem->UID); ?>">
                                <?php  echo $actionItem->Name; ?>
                            </button>
                            </button>

                        </span>
                    </div>
                </div>

                <!-- CARD BODY WITH INFORMATION AND COMMAND BUTTONS -->
                <div class="card-body" style="padding:6px;">
                    <div>Run at <?php  echo $actionItem->Trigger->Info; ?></div>
                    <div><?php  echo $actionItem->GetActionString(); ?></div>

                    <div style="text-align:center">
                        <button class="btn btn-light" type="submit" name="cmdactiondelete" value="1"><i
                                class="fas fa-trash-alt"></i></button>
                        <button class="btn btn-light" type="submit" name="cmdactiontest" value="1"><i
                                class="fas fa-bug"></i></button>
                        <button class="btn btn-light" type="submit" name="cmdactionduplicate" value="1"><i
                                class="fas fa-copy"></i></button>

                    </div>

                </div>
            </div>




            <!-- MODAL FORM USED TO EDIT THE ACTION -->
            <div class="modal fade" id="Modal_<?php echo ($actionItem->UID); ?>" tabindex="-1" role="dialog"
                aria-labelledby="exampleModalLongTitle" aria-hidden="true">


                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">

                        <!-- MODAL HEADER -->
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">Editing <?php  echo $actionItem->Name; ?>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <!-- MODAL BODY -->
                        <div class="modal-body">

                            <div class="mx-auto px-5">

                                <!-- DISPLAY THE NAME OF THE TASK -->
                                <div class="form-group row">
                                    <label for="name" class="col-sm-1 col-form-label">Name</label>
                                    <div class="col-sm">
                                        <input type="text" class="form-control" id="actionname_<?php echo ($actionItem->UID); ?>" name="actionname"
                                            placeholder="Action Name" aria-describedby="NameHelp" value="<?php  echo $actionItem->Name; ?>">
                                    </div>
                                </div>


                                <!-- DISPLAY THE TRIGGER TYPE -->
                                <div class="form-group row">

                                    <label for="Trigger" class="col-sm-1 col-form-label">Trigger</label>

                                    <div class="col-sm">
                                        <select name="triggerType" class="form-control"
                                            id="triggerType_<?php echo ($actionItem->UID); ?>"
                                            onchange="TASKFORM.ToogleTriggerProperties('<?php echo ($actionItem->UID); ?>')">


                                            <?php foreach( trigger::GetTriggers() as $Key => $Value) : ?>
                                                <option class="form-control" value="<?php echo $Key; ?>" <?php echo $actionItem->Trigger->TriggerType == $Key? 'selected':'' ?> ><?php echo $Value; ?></option>
                                            <?php endforeach; ?>

                                        </select>
                                    </div>

                                    <!-- CASE THIS IS A TIME BASED EVENT -->
                                    <div id="Time_<?php echo ($actionItem->UID); ?>" class="col-sm">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="hour" placeholder="Hours"  id="hour_<?php echo ($actionItem->UID); ?>" value="<?php echo $actionItem->Trigger->Hour ?>">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">H</span>
                                            </div>
                                            <input type="text" class="form-control" name="minute" placeholder="Minutes" id="minute_<?php echo ($actionItem->UID); ?>" value="<?php echo $actionItem->Trigger->Minute ?>">
                                        </div>
                                    </div>

                                    <!-- CASE THIS IS A SUNRISE OR SUNSET BASED EVENT -->
                                    <div id="SunriseSunset_<?php echo ($actionItem->UID); ?>" class="col-sm">
                                        <div class="input-group ">
                                            <div class="input-group-prepend">
                                                <select name="offsetdirection" class="form-control input-group-text" >
                                                    <option value="+" <?php echo $actionItem->Trigger->OffsetDirection=='+' ? 'selected' : '' ?> >+</option>
                                                    <option value="-" <?php echo $actionItem->Trigger->OffsetDirection=='-' ? 'selected' : '' ?> >-</option>
                                                </select>
                                            </div>
                                            <input type="text" class="form-control" name="offsethour" id="offsethour_<?php echo ($actionItem->UID); ?>"
                                                placeholder="Hours" value="<?php echo $actionItem->Trigger->OffsetHour ?>">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">H</span>
                                            </div>
                                            <input type="text" class="form-control" name="offsetminute" id="offsetminute_<?php echo ($actionItem->UID); ?>"
                                                placeholder="Minutes" value="<?php echo $actionItem->Trigger->OffsetMinute ?>">
                                        </div>

                                        <!-- Hide this group by default -->
                                        <script>
                                        $('#SunriseSunset_<?php echo ($actionItem->UID); ?>').hide();
                                        </script>

                                    </div>
                                </div>


                                <!-- DISPLAY THE ACTION TYPE -->
                                <div class="form-group row">
                                    <label for="Action" class="col-sm-1 col-form-label">Action</label>
                                    <div class="col-sm">
                                        <select name="Action" class="form-control input-group-text" id="actionType_<?php echo ($actionItem->UID); ?>"
                                            onchange="TASKFORM.ToogleDevices('<?php echo ($actionItem->UID); ?>');">
                                            <option class="form-control">-</option>'

                                            <?php foreach( Action::GetActionTypes() as $Key => $Value) : ?>
                                                <option class="form-control" value="<?php echo $Key; ?>" <?php echo $actionItem->ActionType == $Key? 'selected':'' ?> ><?php echo $Value; ?></option>
                                            <?php endforeach; ?>

                                        </select>
                                    </div>

                                    <!-- CASE THIS IS A VALUE ACTION -->
                                    <div class="col-sm">
                                        <input type="text" id="setdevicevalue_<?php echo ($actionItem->UID); ?>" class="form-control"
                                            name="setdevicevalue" placeholder="Value (%)" value="<?php echo $actionItem->Value?>">

                                        <script>
                                        $('#setdevicevalue_<?php echo ($actionItem->UID); ?>').hide();
                                        </script>

                                    </div>
                                </div>


                                <!-- LIGHTS DEVICES -->
                                <div class="form-group row" id="device_lights_<?php echo ($actionItem->UID); ?>">
                                    <label for="Action" class="col-sm-1 col-form-label">Devices</label>
                                    <div class="col-sm-11">


                                        <?php foreach($ohInfoMngr->GetLights() as $ohItem): ?>

                                        <span class="form-check form-check-inline" style="width:250px;">
                                            <input type="checkbox" name="lightdevices[]"
                                                value="<?php  echo $ohItem->Name ?>" <?php echo $actionItem->ContainsDevice($ohItem->Name ) ? ' checked ' : '' ?>/>
                                            <label class="form-check-label"
                                                for="inlineCheckbox2"><?php  echo $ohItem->Label ?></label>
                                        </span>

                                        <?php endforeach; ?>



                                        <!-- Hide this group by default -->
                                        <script>
                                        $('#device_lights_<?php echo ($actionItem->UID); ?>').hide();
                                        </script>

                                    </div>
                                </div>

                                <div class="form-group row" id="device_rollers_<?php echo ($actionItem->UID); ?>">
                                    <label for="Action" class="col-sm-1 col-form-label">Devices</label>
                                    <div class="col-sm-11">

                                        <?php foreach($ohInfoMngr->GetRollers() as $ohItem): ?>

                                        <span class="form-check form-check-inline" style="width:250px;">
                                            <input type="checkbox" name="rollerdevices[]"
                                                value="<?php  echo $ohItem->Name ?>" <?php echo $actionItem->ContainsDevice($ohItem->Name ) ? ' checked ' : '' ?>/>
                                            <label class="form-check-label"
                                                for="inlineCheckbox2"><?php  echo $ohItem->Label ?></label>
                                        </span>

                                        <?php endforeach; ?>

                                        <!-- Hide this group by default -->
                                        <script>
                                        $('#device_rollers_<?php echo ($actionItem->UID); ?>').hide();
                                        </script>
                                    </div>

                                </div>

                                <div class="form-group row" id="device_setvalue_<?php echo ($actionItem->UID); ?>">
                                    <label for="Action" class="col-sm-1 col-form-label">Devices</label>
                                    <div class="col-sm-11">

                                        <?php foreach($ohInfoMngr->GetSetValues() as $ohItem): ?>

                                        <span class="form-check form-check-inline" style="width:250px;">
                                            <input type="checkbox" name="setvaluedevices[]"
                                                value="<?php  echo $ohItem->Name ?>" <?php echo $actionItem->ContainsDevice($ohItem->Name ) ? ' checked ' : '' ?>/>
                                            <label class="form-check-label"
                                                for="inlineCheckbox2"><?php  echo $ohItem->Label ?></label>
                                        </span>

                                        <?php endforeach; ?>

                                    </div>

                                </div>
                                <!-- Hide this group by default -->
                                <script>
                                $('#device_setvalue_<?php echo ($actionItem->UID); ?>').hide();
                                </script>

                            </div>




                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="cmdtempsave" onclick="return TASKFORM.ValidateTrigger('<?php echo ($actionItem->UID); ?>')">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>


    <!-- THIS SCRIPTS ALLOWS CONTENT TO BE ADAPTED TO SELECTED DROPDOWN VALUES -->
    <script>
        TASKFORM.ToogleTriggerProperties('<?php echo ($actionItem->UID); ?>');
        TASKFORM.ToogleDevices('<?php echo ($actionItem->UID); ?>');

    </script>

    <?php endforeach; ?>
</div>