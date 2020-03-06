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

<script>
// $(document).ready(function() {
//      // make following action fire when radio button changes
//      $('input[type=radio]').change(function(){
//           // find the submit button and click it on the previous action
//           $('input[type=submit]').click()
//           });
//      });
//     
</script>





<div class="mx-auto px-5 ">



    <div class="table-responsive-sm">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col"> <a href="TaskNew.php">
                    <button class="btn " type="submit" name="cmdDelete" value="1" style="background-color:#FF6600;color:white;font-weight:bold"><i
                                class="fas fa-plus" style="padding-right:10px"> </i> New action</button>
                    
                     <!-- <i class="fas fa-plus"> </i> New</a> -->
                    </th>
                    <!-- <th scope="col">Name</th> -->
                    <th scope="col">Comment</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php $Storage=TaskStorage::LoadTaskStorage();
                        foreach ($Storage->TaskStorageItems as &$item) {
                            echo '<form action="Default.php" method="post">'
                ?>
                <input type="hidden" name="ItemID" value="<?php echo ($item->UID); ?>">

                <tr>
                    <th scope="row"> <a
                            href="/TaskEdit.php?itemID=<?php echo ($item->UID); ?> "><?php echo ($item->Name); ?></a>
                    </th>
                    <!-- <td><?php echo ($item->Name); ?></td> -->
                    <td><?php echo ($item->Comment); ?></td>

                    <td>
                        <button class="btn btn-light" type="submit" name="cmdDelete" value="1"><i
                                class="fas fa-trash-alt"></i></button>
                        <!-- <button class="btn btn-light btn-lg" type="submit" name="cmdEdit" value="1"><i class="fas fa-edit"></i></button> -->
                        <button class="btn btn-light" type="submit" name="cmdTest" value="1"><i
                                class="fas fa-bug"></i></button>
                        <button class="btn btn-light" type="submit" name="cmdDuplicate" value="1"><i
                                class="fas fa-copy"></i></button>
                        <button class="btn btn-light" type="submit" name="cmdToogle" value="off"><i
                                class="fas <?php echo ($item->IsEnabled) ?  'fa-toggle-on' :  'fa-toggle-off'  ?>"></i></button>


                    </td>

                </tr>

                <?php echo '</form>'; } ?>

            </tbody>


        </table>


    </div>

</div>



</form>