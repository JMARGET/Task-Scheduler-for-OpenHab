<?php

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

    $Tasks=StorageManager::LoadTasks();
    $TaskItem=$Tasks->GetItem($ItemId);

    foreach ($TaskItem->Actions as $Action){
        $Action->RunAction();
    }


}


function DeleteTaskItem ($ItemId){

    $Tasks=StorageManager::LoadTasks();
    $Tasks->Remove($ItemId);
    StorageManager::Save($Tasks);
}

function ToogleTaskItem ($ItemId){

    $Tasks=StorageManager::LoadTasks();

    $TaskItem=$Tasks->GetItem($ItemId);

    $TaskItem->IsEnabled=$TaskItem->IsEnabled ? false: true;
  
    StorageManager::Save($Tasks);

}

function DuplicateTaskItem ($ItemId){

    $Tasks=StorageManager::LoadTasks();
    $TaskItem=$Tasks->DuplicateTask($ItemId);
    StorageManager::Save($Tasks);

}

function RedirectToEditPage ($ItemID){

    header('Location:taskview.php?idtask='. $ItemID );
    exit;
}

?>


<?php $Storage=StorageManager::LoadTasks();
    $ColSize=$_GET['view'] ?? 6 ;
?>


<!-- <div class="w-100"></div> -->

<div class="container-fluid">
    <div class="row">

        <!-- Go Through each element in the collection and display its card -->
        <?php foreach($Storage->Tasks as $TaskItem): ?>

        <div class="col-sm-<?php echo $ColSize;?> mb-2">
            <form action="" method="post" style="margin:2px;">
                <div class="card">
                    <div class="card-header" style="padding:6px;">
                        <div class="row">
                            <div class="col-7">
                                <a class="font-weight-bold"
                                    href="/taskview.php?idtask=<?php echo ($TaskItem->UID); ?> "><?php echo ($TaskItem->Name); ?>
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