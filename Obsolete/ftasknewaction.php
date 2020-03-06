<div class="mx-auto px-5">

    <!-- DISPLAY THE NAME OF THE TASK -->
    <div class="form-group row">
        <label for="name" class="col-sm-1 col-form-label">Name</label>
        <div class="col-sm">
            <input type="text" class="form-control" id="actionname" name="actionname" placeholder="Action Name" aria-describedby="NameHelp" >
        </div>
    </div>

    <div class="form-group row">

        <label for="Trigger" class="col-sm-1 col-form-label">Trigger</label>

        <div class="col-sm">
            <select name="triggerType" class="form-control" id="triggerType"
                onchange="TASKFORM.ToogleTriggerProperties();">
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
        <label for="Action" class="col-sm-1 col-form-label">Action</label>
        <div class="col-sm">
            <select name="Action" class="form-control input-group-text" id="actionType"
                onchange="TASKFORM.ToogleDevices(null);">
                <option class="form-control">-</option>'
                <?php 

                        $actionTypes = Action::GetActionTypes();
                        
                        for ($i = 0; $i < count($actionTypes); $i++) {
                            echo ('<option class="form-control" value="' . $i . '">' . $actionTypes[$i] . '</option>');
                        }
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


            <?php foreach($ohInfoMngr->GetLights() as $ohItem): ?>

            <span class="form-check form-check-inline" style="width:250px;">
                <input type="checkbox" name="lightdevices[]" value="<?php  echo $ohItem->Name ?>" />
                <label class="form-check-label" for="inlineCheckbox2"><?php  echo $ohItem->Label ?></label>
            </span>

            <?php endforeach; ?>



            <!-- Hide this group by default -->
            <script>
            $('#device_lights').hide();
            </script>

        </div>
    </div>

    <div class="form-group row" id="device_rollers">
        <label for="Action" class="col-sm-1 col-form-label">Devices</label>
        <div class="col-sm-11">

            <?php foreach($ohInfoMngr->GetRollers() as $ohItem): ?>

            <span class="form-check form-check-inline" style="width:250px;">
                <input type="checkbox" name="rollerdevices[]" value="<?php  echo $ohItem->Name ?>" />
                <label class="form-check-label" for="inlineCheckbox2"><?php  echo $ohItem->Label ?></label>
            </span>

            <?php endforeach; ?>

            <!-- Hide this group by default -->
            <script>
            $('#device_rollers').hide();
            </script>
        </div>

    </div>

    <div class="form-group row" id="device_setvalue">
        <label for="Action" class="col-sm-1 col-form-label">Devices</label>
        <div class="col-sm-11">

            <?php foreach($ohInfoMngr->GetSetValues() as $ohItem): ?>

            <span class="form-check form-check-inline" style="width:250px;">
                <input type="checkbox" name="setvaluedevices[]" value="<?php  echo $ohItem->Name ?>" />
                <label class="form-check-label" for="inlineCheckbox2"><?php  echo $ohItem->Label ?></label>
            </span>

            <?php endforeach; ?>

        </div>

    </div>
    <!-- Hide this group by default -->
    <script>
    $('#device_setvalue').hide();
    </script>

    <div class="text-right">
        <input class="btn btn-primary " type="submit" name="cmdtempsave" value="Save Action"
            onclick="return TASKFORM.ValidateTrigger (null);">
    </div>
</div>