<?php 

require 'oh.php';
require 'task.php';
require 'time.php';

//SET THE REPOSITORY FOLDER HERE
define('STORAGEFOLDER' ,  dirname(__DIR__, 1) . '/data') ;

//CHANGE THE NAME OF THE REPOSITORIES HERE
CONST TASKPATH    = STORAGEFOLDER . '/tasks';
CONST TASKBKPPATH = STORAGEFOLDER . '/tasks_bkp';
CONST DEVICEPATH  = STORAGEFOLDER . '/devices';
CONST TIMEPATH    = STORAGEFOLDER . '/time';


// define("FileStorageName", __DIR__ . '/data/tasks');
// define("FileStorageNameBKP", __DIR__ . '/data/tasks_bkp');
// define("DeviceStorageName", __DIR__ . '/data/devices');
// define("TimeInfoStorageName", __DIR__ . '/data/time');
// define("FileStorageNameDBG", __DIR__ . '/data/DataStorageDBG');


class StorageManager{

    //LOADS TIMEINFO FROM THE LOCAL HDD
    public static function LoadTimeInfo ():TimeInfo{

        $FileData = file_get_contents(TIMEPATH);

        if(empty($FileData))
        {
            return new TimeInfo;
        }else{
            return unserialize($FileData);
        }            
        
    }

    //LOADS TASKS FROM THE LOCAL HDD
    public static function LoadTasks():TasksManager{
        $Retval=new TasksManager;

        try{

            $FileData = file_get_contents(TASKPATH);

            if(empty($FileData))
            {
                throw new ErrorException('Failed to read data');
            }            
            
            $Retval = unserialize($FileData);

        }catch(Exception $e){
            return new TasksManager;
        }

        return $Retval;

    }

    //LOADS OPENHAB INFO FROM THE LOCAL HDD
    public static function LoadOHInfo ():OHItemManager{

        $FileData = file_get_contents(DEVICEPATH);

        if(empty($FileData))
        {
            return new OHItemManager;
        }else{
            return unserialize($FileData);
        }  

    }

    //SAVES THE GIVEN OBJECT TO THE LOCAL HDD
    public static function Save ($Item){

        $ObjectClass=get_class($Item);
              
        switch ($ObjectClass) {

            //SAVES TASKS TO THE LOCAL HDD
            case TasksManager::class:
                $SerializedData= serialize($Item);
                file_put_contents(TASKPATH, $SerializedData);
                file_put_contents(TASKBKPPATH. "_". date( "Y-m-d",strtotime("now")) , $SerializedData);
                break;

            //SAVES TIMEINFO TO THE LOCAL HDD    
            case TimeInfo::class:
                $Item->LastUpdated=strtotime("now");
                $SerializedData= serialize($Item);
                file_put_contents(TIMEPATH, $SerializedData);
                break;

            //SAVES OPENHAB INFO TO THE LOCAL HDD
            case OHItemManager::class:
                $SerializedData= serialize($Item);
                file_put_contents( DEVICEPATH, $SerializedData);
                break;
        }

    }


    
}


?>