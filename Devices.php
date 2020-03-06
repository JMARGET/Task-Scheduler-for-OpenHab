<?php

//THOSE ARE REQUIRED SO THAT THE PAGE IS COMPLIANT WITH THE SUBCLASSES
require 'global.php';
require './classes/storage.php';


if (IsPostBack()){
    if (!empty($_POST['cmdRefresh'])){
        $OHItemMngr = OHItemManager::LoadFromOpenHab($_POST['ohserverurl'],$_POST['ohitemsTags'] ?? null,$_POST['ohitemsFields'] ?? null);
        StorageManager::Save( $OHItemMngr);
  
    }

    if (!empty($_POST['cmdToogle'])){
        ToogleTaskItem($_POST['ItemID']);
    }

    if (!empty($_POST['cmdEdit'])){
        RedirectToEditPage($_POST['ItemID']);
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

<?php
    $OHItemMngr = StorageManager::LoadOHInfo();

    // var_dump($OHItemMngr->GetSwitches());
    // var_dump($OHItemMngr->GetDimmers());
    // var_dump($OHItemMngr->GetRollers());
?>

<div class="mx-auto px-5 ">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"  method="post" Name="RefreshParameters" onsubmit="return DEVICESFORM.ValidateTrigger ()" >

            <!-- <div class="form-group">
                <label for="exampleInputEmail1">OpenHab Server URL & Port</label>
                <input type="text" class="form-control" Name="ohserverurl" aria-describedby="URLHelp"
                    placeholder="http:\\192.168.10.150:8081" Value="<?php echo ($OHItemMngr->OHServerURL ?? null)  ?>">
            </div> -->


            <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">OpenHab Server URL & Port</div>
                        </div>
                        <input type="text" class="form-control" Name="ohserverurl"
                            placeholder="http:\\192.168.10.150:8081" Value="<?php echo ($OHItemMngr->OHServerURL ?? null)  ?>">
                    </div>


            <div class="form-row ">
                <div class="col-lg">
                    <label class="sr-only" for="inlineFormInputGroup">Tags</label>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Tags (optionnal)</div>
                        </div>
                        <input type="text" class="form-control" Name="ohitemsTags"
                            placeholder="Lighting, Blinds, Schedulable..." Value="<?php echo($OHItemMngr->OHItemsTags ?? null)  ?>">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary mb-2" name="cmdRefresh" value=1 >Download devices</button>
                </div>
            </div>


    </form>

    <form>

            <div class="alert alert-success">
                <strong>Info!</strong> The following devices will be available while you'll configure any new task.
            </div>

            <table class="table table-sm">
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Label</th>
                        <th scope="col">Type</th>
                        <th scope="col">Link</th>
                    </tr>
                </thead>
                <tbody>

                    <?php 
        


        foreach ($OHItemMngr->OHItems as $OHItem){
            echo ('<tr>');
            echo ('<th scope="row">' . $OHItem->Name . '</th>');
            echo ('<td>' . $OHItem->Label . '</td>');
            echo ('<td>' . $OHItem->GetType() . '</td>');
            echo ('<td><a href="' . $OHItem->Link . '">' . $OHItem->Link .'</a></td>');
            echo ('</tr>');
        }
        
        ?>

                </tbody>
            </table>


    </form>


</div>




</body>

</html>