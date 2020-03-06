<?php
session_start();
define("FileStorageName", __DIR__ . '/data/#1.DataStorage');
define("FileStorageNameBKP", __DIR__ . '/data/BKP_DataStorage');
define("FileStorageNameDBG", __DIR__ . '/data/DataStorageDBG');
define("DeviceStorageName", __DIR__ . '/data/#2.DeviceStorage');
define("TimeInfoStorageName", __DIR__ . '/data/#3.TimeInfoStorage');

/**
 * TASK STORAGE
 * @package    TASK STORAGE
 * @author     JEROME MARGET
 */
class TaskStorage{

    public $TaskStorageItems=array();

    function __construct() {}


    public function Add($TaskItem){

        array_push($this->TaskStorageItems,$TaskItem);

    }

    public function Remove($TaskItemId){

        $this->TaskStorageItems=array_filter($this->TaskStorageItems,  function ($e) use (&$TaskItemId) {
            return $e->UID!= $TaskItemId;
        });


    }

    public function GetItem($TaskItemId):DailyTask{

        $FileteredArray = array_filter($this->TaskStorageItems,  function ($e) use (&$TaskItemId) {
            return $e->UID== $TaskItemId;
        });
       
        return reset($FileteredArray);
    
    }

    public function Save(){
        $SerializedData= serialize($this);
        file_put_contents(constant('FileStorageName'), $SerializedData);
        file_put_contents(constant('FileStorageNameBKP'). "_". date( "Y-m-d",strtotime("now")) , $SerializedData);
       
    }

    public function GetTasksToRun ($RunAt):array{
        $Retval = array();
        $TimeInfo = TimeInfo::Load();

        foreach ($this->TaskStorageItems as $Item){

            //Check if the Task is enabled first
            if($Item->IsEnabled){
              
                //Check if today is a skipped day
                if (!$Item->IsSkipped($RunAt)){

                    if (in_array(date("N"),$Item->Days)){

                        //Check that the Trigger is set
                        if (isset($Item->Trigger)){

                            //Check if the trigger time is actually now
                            if ($Item->Trigger->MustExecuteAt($RunAt)){
                                array_push($Retval,$Item);
                            }

                        }

                    }
                }
           
            }

        }

        return $Retval;

    }

    public function GetTasksOnDate ($Date):array{
        $Retval = array();
        $Day=date ('N', $Date);

  
        //RUN THROUGH ALL ITEMS IN THE DATABASE
        foreach ($this->TaskStorageItems as $Item){
            $DailyTaskItem= new DailyTask();
            $DailyTaskItem=$Item;

            //FIRST MAKE SURE THE TASK IS ENABLED
            if ($DailyTaskItem->IsEnabled){

                //CHECK IF THE ITEM DAYS ARRAY CONTAIN THE SPECIFIED DAY
                if(in_array($Day,$DailyTaskItem->Days)){

                    array_push($Retval,$Item);
                }

                
            }

        }

        //SORT BY HOUR AND TIME BEFORE RETURNING THE ARRAY
        usort($Retval,function ($a, $b){
            if($a->Trigger->Time>$b->Trigger->Time){
                return 1;
            }else{
                return -1;
            }

        });

        
        return $Retval;


    }

    public static function LoadTaskStorage ():TaskStorage{

        $_Retval=new TaskStorage;

        try{

            $FileData = file_get_contents(constant('FileStorageName'));

            if(empty($FileData))
            {
                throw new ErrorException('Failed to read data');
            }            
            
            $_Retval = unserialize($FileData);

        }catch(Exception $e){
            return new TaskStorage;
        }

        return $_Retval;

    }

    //CALL THIS TO DUPLICATE A PARTICULAR TASK
    Public function DuplicateTask($TaskItemId):DailyTask{
        $retval=clone $this->GetItem($TaskItemId);
        
        if (isset($retval)){
            $retval->initOrReset();
            $retval->Name=$retval->Name . ' (copy)';
            $this->Add($retval);
        }
        
        return $retval;
        

    }

    public static function ResetDebugDB ():TaskStorage{

        $_Retval=new TaskStorage;

        $FileData = file_get_contents( constant('FileStorageNameDBG'));
        
        if(empty($FileData))
        {
            throw new ErrorException('Failed to read data');
        }      
        
        $_Retval = unserialize($FileData);
        $_Retval->Save();

        return $_Retval;


    }


}

/**
 * DAILYTASK
 * @package    DAILYTASK
 * @author     JEROME MARGET
 */
class DailyTask{
    public $Name;
    public $IsEnabled;
    protected $UID;
    protected $CreatedOn;
    protected $LastUpdated;
    public $Days=array();
    public $SkippedDays=array();
    public $Comment;
    public $Trigger;
    public $Action;
    protected $ActionValue;

    function __construct() {

        $this->initOrReset();
        $this->Trigger=new TimeBasedTrigger();
    }

    public function __get ($Property) {

        if ($Property=="CreatedOn"){

            return $this->CreatedOn?? null;
        }

        if ($Property=="UID"){

            return $this->UID?? null;
        }

    }

    public function __set( $key, $value ) {}


    public function Skip($iDay):bool{
        $retval=false;
        $fDate=date('Y-z', $iDay);
        if (!in_array($fDate,$this->SkippedDays)){
            array_push($this->SkippedDays,$fDate);
            $retval=true;
        }
        return $retval;
    }
   
    public function  RemoveSkipped($iDay):bool{
        $retval=false;
        $fDate=date('Y-z', $iDay);
        $key = array_search($fDate, $this->SkippedDays);
        if ($key !== false) {
            unset($this->SkippedDays[$key]);
            $retval=true;
        }
        return $retval;

    }

    public function IsSkipped($iDay):bool{
        $fDate=date('Y-z', $iDay);
        if (in_array( $fDate,$this->SkippedDays)){
            return true;
        }else{
            return false;
        }


    }

    public function initOrReset(){
        $this->CreatedOn=new DateTime('now');
        $this->UID=uniqid();
    }


}


/**
 * TRIGGER
 * @package    TRIGGER
 * @author     JEROME MARGET
 */
abstract class Trigger{
    const TIME = 0;
    const SUNRISE = 1;
    const SUNSET = 2;
    
    protected $TriggerType;

    public static function GetTriggers(){
        return array(Trigger::TIME=>'Time',Trigger::SUNRISE=>'Sunrise',Trigger::SUNSET=>'Sunset');
    }

    public function MustExecuteAt($RunAt):bool{
        if((date('H',$RunAt)==date('H',$this->Time)) && (date('i',$RunAt)==date('i',$this->Time)) ){
            return true;
        }else {
            return false;
        }
    }

    public function __get ($Property) {



    }

}

class TimeBasedTrigger extends Trigger{
    protected $Hour=0;
    protected $Minute=0;

    function __construct() {
        $this->TriggerType=Trigger::TIME ;
    }

    public function __get ($Property) {

        if ($Property=="Hour"){
            return $this->Hour ?? null;
        }

        if ($Property=="Minute"){
            return $this->Minute ?? null;
        }

        if ($Property=="TriggerType"){
            return $this->TriggerType ?? null;
        }

        if ($Property=="Time"){
            return mktime($this->Hour , $this->Minute);
        }

        if ($Property=="FormattedTime"){
            return date("H:i",$this->Time) ?? null;
        }
    }

    public function __set( $key, $value ) {
        if ($key=="Hour"){

            if (preg_match("/^(?:2[0-3]|[01][0-9]|[0-9])$/",$value)===1 ){
                $this->Hour=intval($value);
            }else {
                throw new Exception ("Invalid Hours");
            }
        }

        if ($key=="Minute"){

            if (preg_match("/^([0-5][0-9]|[0-9])$/",$value)===1 ){
                $this->Minute=intval($value);
            }else {
                throw new Exception ("Invalid Minutes");
            }
        }

    }

}

class SunriseBasedTrigger extends Trigger{
    protected $OffsetHour=0;
    protected $OffsetMinute=0;
    protected $OffsetDirection;

    function __construct() {
        $this->TriggerType=Trigger::SUNRISE ;
        $this->OffsetDirection="+";
    }

    public function __get ($Property) {

        if ($Property=="OffsetHour"){
            return $this->OffsetHour ?? null;
        }

        if ($Property=="OffsetMinute"){
            return $this->OffsetMinute ?? null;
        }

        if ($Property=="OffsetDirection"){
            return $this->OffsetDirection ?? null;
        }

        if ($Property=="TriggerType"){
            return $this->TriggerType ?? null;
        }

        if ($Property=="Time"){
            
            $tmeInfo=TimeInfo::Load();

            $xTime = mktime(intval($tmeInfo->SunriseHour),intval($tmeInfo->SunriseMinute));

            $strToTimeString=$this->OffsetDirection . intval($this->OffsetHour) . ' hours, ' . $this->OffsetDirection . intval($this->OffsetMinute) . ' minutes' ;

            $uTime= strtotime($strToTimeString,$xTime);

            $temp=date('H:i',$uTime);
            
            return $uTime;

        }

        if ($Property=="FormattedTime"){
            return date("H:i",$this->Time) ?? null;
        }
        
    }

    public function __set( $key, $value ) {
        if ($key=="OffsetHour"){

            if (preg_match("/^(?:2[0-3]|[01][0-9]|[0-9]|)$/",$value)===1 ){
                $this->OffsetHour=intval($value);
            }else {
                throw new Exception ("Invalid Hours");
            }
        }

        if ($key=="OffsetMinute"){

            if (preg_match("/^([0-5][0-9]|[0-9]|)$/",$value)===1 ){
                $this->OffsetMinute=intval($value);
            }else {
                throw new Exception ("Invalid Minutes");
            }
        }

        
        if ($key=="OffsetDirection"){

            if (preg_match("/^(?:\+|\-)$/",$value)===1 ){
                $this->OffsetDirection=$value;
            }else {
                throw new Exception ("Invalid Offset (Expected + or -)");
            }
        }

    }

}

class SunsetBasedTrigger extends Trigger{
    protected $OffsetHour=0;
    protected $OffsetMinute=0;
    protected $OffsetDirection;

    function __construct() {
        $this->TriggerType=Trigger::SUNSET ;
        $this->OffsetDirection="+";
    }

    public function __get ($Property) {

        if ($Property=="OffsetHour"){
            return $this->OffsetHour  ?? null;
        }

        if ($Property=="OffsetMinute"){
            return $this->OffsetMinute ?? null;
        }

        if ($Property=="OffsetDirection"){
            return $this->OffsetDirection ?? null;
        }

        if ($Property=="TriggerType"){
            return $this->TriggerType ?? null;
        }

        if ($Property=="Time"){
            $tmeInfo=TimeInfo::Load();

            $xTime = mktime(intval($tmeInfo->SunsetHour),intval($tmeInfo->SunsetMinute));

            $strToTimeString=$this->OffsetDirection . intval($this->OffsetHour) . ' hours, ' . $this->OffsetDirection . intval($this->OffsetMinute) . ' minutes' ;

            $uTime= strtotime($strToTimeString,$xTime);

            $temp=date('H:i',$uTime);
            
            return $uTime;

           
        }

        if ($Property=="FormattedTime"){
            return date("H:i",$this->Time) ?? null;
        }

    }

    public function __set( $key, $value )    {
        if ($key=="OffsetHour"){

            if (preg_match("/^(?:2[0-3]|[01][0-9]|[0-9]|)$/",$value)===1 ){
                $this->OffsetHour=intval($value);
            }else {
                throw new Exception ("Invalid Hours");
            }
        }

        if ($key=="OffsetMinute"){

            if (preg_match("/^([0-5][0-9]|[0-9]|)$/",$value)===1 ){
                $this->OffsetMinute=intval($value);
            }else {
                throw new Exception ("Invalid Minutes");
            }
        }

        if ($key=="OffsetDirection"){

            if (preg_match("/^(?:\+|\-)$/",$value)===1 ){
                $this->OffsetDirection=$value;
            }else {
                throw new Exception ("Invalid Offset (Expected + or -)");
            }
        }

    }

}


/**
 * ACTION
 * @package    ACTION
 * @author     JEROME MARGET
 */
abstract class Action{

    const OPEN = 0;
    const CLOSE = 1;
    const SWITHON = 2;
    const SWITHOFF = 3;
    const SETVALUE = 4;

    public $DeviceNames=array();
    public $DeviceLinks=array();

    public static function GetActionTypes(){
        return array(Action::OPEN=>'Open',Action::CLOSE=>'Close',Action::SWITHON=>'Switch On',Action::SWITHOFF=>'Switch Off',Action::SETVALUE=>"Set value (%)");
    }

    public static function GetActionType($ActionNumber){
        switch ($ActionNumber){
            case 0: return ActionTypes::OPEN;
            case 1: return ActionTypes::CLOSE;
            case 2: return ActionTypes::SWITHON;
            case 3: return ActionTypes::SWITHOFF;
            case 4: return ActionTypes::SETVALUE;

        }
    }
 
    abstract protected function RunAction();

    function __construct($Devices) {
        $this->DeviceNames=$Devices;
    }

   public static function sendPostCommand($url, $data) {
      
        $options = array(
          'http' => array(
              'header'  => "Content-type: text/plain\r\n",
              'method'  => 'POST',
              'content' =>strval( $data)  //http_build_query($data),
          ),
        );
      
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
      
        return $result;
    }
    
}

class ActionOPEN extends Action {

    public function RunAction(){

        foreach($this->DeviceLinks as $DeviceLink){
            Action::sendPostCommand($DeviceLink,"UP");
        }

    }

    public function __get ($Property) {

        if ($Property=="ActionType"){
            return Action::OPEN ?? null;
        }   
    }

}

class ActionCLOSE extends Action {

    public function RunAction(){
        foreach($this->DeviceLinks as $DeviceLink){
            Action::sendPostCommand($DeviceLink,"DOWN");
        }
    }

    public function __get ($Property) {

        if ($Property=="ActionType"){
            return Action::CLOSE ?? null;
        }   
    }
}

class ActionSWITCHON extends Action {

    public function RunAction(){
        foreach($this->DeviceLinks as $DeviceLink){
            Action::sendPostCommand($DeviceLink,"ON");
        }
    }


    public function __get ($Property) {

        if ($Property=="ActionType"){
            return Action::SWITHON ?? null;
        }   
    }
}

class ActionSWITCHOFF extends Action {

    public function RunAction(){
        foreach($this->DeviceLinks as $DeviceLink){
            Action::sendPostCommand($DeviceLink,"OFF");
        }
    }

    public function __get ($Property) {

        if ($Property=="ActionType"){
            return Action::SWITHOFF ?? null;
        }   
    }
}

class ActionSETVALUE extends Action {
    protected $Value;

    function __construct($Devices,$val) {
        $this->DeviceNames=$Devices;
        $this->SetActionValue($val);
    }

    public function __set( $key, $value )
    {
        if ($key=="Value"){
           $this-> SetActionValue($value);

        }

    }

    private function SetActionValue($val){
  
        if (preg_match("/^([0-9]|[0-9][0-9]|[0-1][0-0][0-0])$/",$val)===1 ){
            $this->Value=$val;
        }else {
            throw new Exception ("Invalid Value");
        }
   
    }

    public function RunAction(){
        foreach($this->DeviceLinks as $DeviceLink){
            Action::sendPostCommand($DeviceLink,$this->Value);
        }
    }

    public function __get ($Property) {

        if ($Property=="ActionType"){
            return Action::SETVALUE ?? null;
        }

        if ($Property=="Value"){
            return $this->Value ?? null;
        }    
    }



}



class OHItemManager{
    public $OHItems=array();
    public $OHServerURL;
    public $OHItemsTags;
    public $OHItemsFields;

    public function GetSwitches():array{
        $Retval;

         $Retval= array_filter($this->OHItems,function ($e){
            if( get_class($e) == get_class(new OHItemSwitch())){
                return 1;
            } else{
                return 0;
            }
            
         });

         return $this->SortDevices($Retval);

    }

    public function GetLights():array{
        $Retval;

         $Retval= array_filter($this->OHItems,function ($e){
            if( get_class($e) == get_class(new OHItemSwitch()) || get_class($e) == get_class(new OHItemDimmer())){

                return 1;
                
            } else{
                return 0;
            }
            
         });

  
         return $this->SortDevices($Retval);

    }

    public function GetSetValues():array{
        $Retval;

         $Retval= array_filter($this->OHItems,function ($e){
            if( get_class($e) == get_class(new OHItemDimmer()) || get_class($e) == get_class(new OHItemRollerShutter())){

                return 1;
                
            } else{
                return 0;
            }
            
         });

  
         return $this->SortDevices($Retval);

    }

    public function GetRollers():array{
        $Retval;

         $Retval= array_filter($this->OHItems,function ($e){
            if( get_class($e) == get_class(new OHItemRollerShutter())){
                return 1;
            } else{
                return 0;
            }
            
         });

         return $this->SortDevices($Retval);

    }

    public function GetDimmers():array{
        $Retval;

         $Retval= array_filter($this->OHItems,function ($e){
            if( get_class($e) == get_class(new OHItemDimmer())){
                return 1;
            } else{
                return 0;
            }
            
         });

         return $this->SortDevices($Retval);

    }

    public function GetLinksForDevices($DevicesNames):array {
        $Retval=array();;

        foreach($DevicesNames as $DeviceName){

            $key = array_search($DeviceName, array_column($this->OHItems, 'Name'));

            if (isset($this->OHItems[$key])){
                array_push($Retval,$this->OHItems[$key]->Link);
            }

        }

        return $Retval;


    }

    private function SortDevices($ArrayToSort):array{
        usort($ArrayToSort,function($a,$b){
        return strcmp($a->Name, $b->Name);
       });

       return $ArrayToSort;
    }

    private static function Compare ($a,$b):int{

        return strcmp($a->Name, $b->Name);
    }

    public function Save(){
        $SerializedData= serialize($this);
        file_put_contents( constant('DeviceStorageName'), $SerializedData);
    }

    public static function LoadFromLocalFile ():OHItemManager{

        $FileData = file_get_contents(constant('DeviceStorageName'));

        if(empty($FileData))
        {
            return new OHItemManager;
        }else{
            return unserialize($FileData);
        }  

    }

    public static function LoadFromOpenHab($url,$tags,$fields):OHItemManager{
        $Retval = new OHItemManager();

        $Retval->OHServerURL=$url;
        $Retval->OHItemsTags=$tags;
        $Retval->OHItemsFields=$fields;

        $Request=$url;
        $Request.="/rest/items?recursive=true";
        $Request.= !empty($tags) ? ("&tags=" . $tags) : null;
        $Request.= !empty($fields) ? ("&fields=" . $fields) : null;

        $FileData = file_get_contents($Request);
    
        $DecodedData = json_decode($FileData, true);

        foreach($DecodedData as $item){
            $Item=OHItem::CreateNew( $item["name"], isset( $item["type"]) ? $item["type"] : "" , isset( $item["label"]) ? $item["label"] : "" , isset( $item["link"]) ? $item["link"] : "");
            

            if(isset($Item)){
                array_push($Retval->OHItems,$Item);
            }
            
        }

        $Retval->OHItems= $Retval->SortDevices($Retval->OHItems);

        return $Retval;
    }
}


abstract class OHItem {
    public $Name;
    public $Label;
    public $Link;

    public static function CreateNew($iName,$iType,$iLabel,$iLink){
        $Retval;

        switch (strtolower ($iType)){
            case "switch" :
                $Retval=new OHItemSwitch();
            break;
            case "dimmer" :
                $Retval=new OHItemDimmer();
            break;
            case "rollershutter":
                $Retval=new OHItemRollerShutter();
            break;


        }

        if (isset($Retval)){
            $Retval->Name=$iName;
            $Retval->Label=empty($iLabel) ? $iName : $iLabel ;
            $Retval->Link=$iLink;
    
            return $Retval;
        }

    }

    abstract public function GetType():string;
    
}

class OHItemSwitch extends OHItem{
    public function GetType():string{return "Switch";}
}

class OHItemDimmer extends OHItem{
    public function GetType():string{return "Dimmer";}
}

class OHItemRollerShutter extends OHItem{
    public function GetType():string{return "Roller/Shutter";}
    
}



class TimeInfo{

    protected $Sunrise;
    protected $Sunset;
    protected $LastUpdated;


    public function __get ($Property) {

        // SUNRISE INFO
        if ($Property=="Sunrise"){
            return $this->Sunrise ?? null;
        } 

        if ($Property=="SunriseHour"){

            return date('H',$this->Sunrise) ?? null;
        } 

        if ($Property=="SunriseMinute"){

            return date('i',$this->Sunrise) ?? null;
        } 


        // SUNSET INFO
        if ($Property=="Sunset"){
            return $this->Sunset ?? null;
        }   

        if ($Property=="SunsetHour"){

            return date('H',$this->Sunset) ?? null;
        } 

        if ($Property=="SunsetMinute"){

            return date('i',$this->Sunset) ?? null;
        } 


        if ($Property=="LastUpdated"){

            return $this->LastUpdated ?? null;
        } 


    }

    public function __set( $Property, $Value ) {
        if ($Property=="Sunrise"){
            $this->Sunrise=strtotime($Value);
        }

        if ($Property=="Sunset"){
            $this->Sunset=strtotime($Value);
        }

    }

    Public function GetFormattedSunrise():string{
        $Retval = ((date('H',$this->Sunrise) ?? null) . 'h' . (date('i',$this->Sunrise) ?? null));

        return $Retval;
    }

    Public function GetFormattedSunset():string{
        $Retval = ((date('H',$this->Sunset) ?? null) . 'h' . (date('i',$this->Sunset) ?? null));

        return $Retval;
    }

    public function Save(){
        $this->LastUpdated=strtotime("now");
        $SerializedData= serialize($this);
        file_put_contents(constant('TimeInfoStorageName'), $SerializedData);
        unset($_SESSION["TimeInfo"]);

    }

    private static function LoadFromLocalFile ():TimeInfo{

        $FileData = file_get_contents(constant('TimeInfoStorageName'));

        if(empty($FileData))
        {
            return new TimeInfo;
        }else{
            return unserialize($FileData);
        }            
        
    }

    public static function Load ():TimeInfo{
        if(!isset($_SESSION["TimeInfo"])){
            
            $_SESSION["TimeInfo"]=TimeInfo::LoadFromLocalFile();

        }

        return $_SESSION["TimeInfo"];

    }
    

}

?>