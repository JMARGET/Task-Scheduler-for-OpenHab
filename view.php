<?php
    require 'global.php';
    require './classes/storage.php';

    //Makes sure that enough details are provided through VAR_Dump()
    ini_set('xdebug.var_display_max_depth', '10');
    ini_set('xdebug.var_display_max_children', '256');
    ini_set('xdebug.var_display_max_data', '1024');

    $TaskMngr=StorageManager::LoadTasks();
    $TimeMngr=StorageManager::LoadTimeInfo();
    $OHMngr=StorageManager::LoadOHInfo();
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

        <div class="" id="accordionViewer">
            <div class="card">
                <div class="card-header" id="headingTask">
                    <h2 class="mb-0">
                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne"
                            aria-expanded="true" aria-controls="collapseOne">
                            Tasks Details
                        </button>
                    </h2>
                </div>

                <div id="collapseOne" class="collapse " aria-labelledby="headingOne" data-parent="#accordionExample">
                    <div class="card-body">
                        <?php  var_dump($TaskMngr); ?>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="headingTwo">
                    <h2 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                            data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Sunset Sunrise Information
                        </button>
                    </h2>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                    <div class="card-body">
                        <?php  var_dump($TimeMngr); ?>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="headingThree">
                    <h2 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                            data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            OpenHab Devices Information
                        </button>
                    </h2>
                </div>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                    <div class="card-body">
                        <?php  var_dump($OHMngr); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <body>

</html>