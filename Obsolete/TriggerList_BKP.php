<?php

require 'GlobalFunctions.php';

if (IsPostBack()){
    if (!empty($_POST['cmdDelete'])){
        DeleteTaskItem($_POST['ItemID']);
    }

    if (!empty($_POST['cmdToogle'])){
        ToogleTaskItem($_POST['ItemID']);
    }

    if (!empty($_POST['cmdEdit'])){
        RedirectToEditPage($_POST['ItemID']);
    }

    if (!empty($_POST['cmdTest'])){
        TestTaskItem($_POST['ItemID']);
    }

    if (!empty($_POST['cmdDuplicate'])){
        DuplicateTaskItem($_POST['ItemID']);
    }

}

function TestTaskItem ($ItemId){

    $Storage=TaskStorage::LoadTaskStorage();
    $TaskItem=$Storage->GetItem($ItemId);
    $TaskItem->Action->RunAction();
}


function DeleteTaskItem ($ItemId){

    $Storage=TaskStorage::LoadTaskStorage();
    $Storage->Remove($ItemId);
    $Storage->Save();
}

function ToogleTaskItem ($ItemId){

    $Storage=TaskStorage::LoadTaskStorage();

    $TaskItem=$Storage->GetItem($ItemId);

    $TaskItem->IsEnabled=$TaskItem->IsEnabled ? false: true;
  
    $Storage->Save();
}

function DuplicateTaskItem ($ItemId){

    $Storage=TaskStorage::LoadTaskStorage();
    $TaskItem=$Storage->DuplicateTask($ItemId);
    $Storage->Save();
    header('Location:TaskEdit.php?itemID='. $TaskItem->UID );
    exit;
}

function RedirectToEditPage ($ItemID){

    header('Location:TaskEdit.php?itemID='. $ItemID );
    exit;
}

?>


<?php $Storage=TaskStorage::LoadTaskStorage();
    $ColSize=$_GET['view'] ?? 3 ;
?>


<!-- <div class="w-100"></div> -->

<div class="container-fluid">
    <div class="card-deck  row">
        <div class="col mb-2">
            <a href="TaskNew.php" style="margin:15px;">
                <button class="btn " type="submit" name="cmdDelete" value="1"
                    style="background-color:#FF6600;color:white;font-weight:bold">
                    <i class="fas fa-plus" style="padding-right:10px"> </i> New action
                </button>
            </a>

        </div>
        <div class="col mb-2 text-right"  style="margin-right:15px;">
        <div class="dropdown show">
                    <a class="btn dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color:#FF6600;color:white;">
                        <?php echo 12/$ColSize . " Columns" ?>
                    </a>

                    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" href="<?php echo getDisplayUrl(12) ?>">1 Column</a>
                        <a class="dropdown-item" href="<?php echo getDisplayUrl(6) ?>"> 2 Columns</a>  
                        <a class="dropdown-item" href="<?php echo getDisplayUrl(4) ?>"> 3 Columns</a>
                        <a class="dropdown-item" href="<?php echo getDisplayUrl(3) ?>"> 4 Columns</a>
                        <a class="dropdown-item" href="<?php echo getDisplayUrl(2) ?>"> 6 Columns</a>
                    </div>

                    <?php 
                        function getDisplayUrl($value){
                            $params=array(
                                'view'=>$value
                            );
                            
                            return  "Default.php?" . http_build_query($params);
                        }
                    ?>

                </div>

        </div>
    </div>
    <div class="card-deck row">

        <!-- Go Through each element in the collection and display its card -->
        <?php foreach($Storage->TaskStorageItems as $TaskItem): ?>

        <div class="col-sm-<?php echo $ColSize;?> mb-2">
            <form action="" method="post" style="margin:2px;">
                <div class="card h-100">
                    <div class="card-header" style="padding:6px;">
                        <div class="row">
                            <div class="col-7">
                                <a class="font-weight-bold"
                                    href="/TaskEdit.php?itemID=<?php echo ($TaskItem->UID); ?> "><?php echo ($TaskItem->Name); ?>
                                </a>
                            </div>
                            <div class="col-5 text-right">

                                <button class="btn btn-light" type="submit" name="cmdToogle" value="off"><i
                                        class="fas <?php echo ($TaskItem->IsEnabled) ?  'fa-toggle-on' :  'fa-toggle-off'  ?>"></i></button>

                            </div>
                        </div>
                    </div>
                    <div class="card-body" style="padding:6px;">

                        <p class="card-text"><?php echo $TaskItem->Comment;?></p>

                        <div style="text-align:center">
                            <input type="hidden" name="ItemID" value="<?php echo ($TaskItem->UID); ?>">
                            <button class="btn btn-light" type="submit" name="cmdDelete" value="1"><i
                                    class="fas fa-trash-alt"></i></button>
                            <button class="btn btn-light" type="submit" name="cmdTest" value="1"><i
                                    class="fas fa-bug"></i></button>
                            <button class="btn btn-light" type="submit" name="cmdDuplicate" value="1"><i
                                    class="fas fa-copy"></i></button>

                        </div>

                    </div>
            </form>
        </div>
    </div>

    <?php endforeach; ?>

</div>
</div>