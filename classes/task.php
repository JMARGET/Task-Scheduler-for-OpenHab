<?php


/**
 * TASKS MANAGER
 * @package    TASKS MANAGER
 * @author     JEROME MARGET
 */
class TasksManager{
    public $Tasks=array();

    function __construct() {}

    //ADD A NEW ITEM TO THE TASKS COLLECTION
    public function Add($Task){

        array_push($this->Tasks,$Task);

    }

    //REMOVES AN ITEM FROM THE TASKS COLLECTION
    public function Remove($TaskId){

        $this->Tasks=array_filter($this->Tasks,  function ($e) use (&$TaskId) {
            return $e->UID!= $TaskId;
        });


    }

    //CALL THIS TO DUPLICATE A PARTICULAR TASK
    Public function DuplicateTask($TaskId):Task{
        $Retval=clone $this->GetItem($TaskId);

        if (isset($Retval)){
            $this->Add($Retval->Clone());
        }
        
        return $Retval;
        

    }

    //RETURNS THE ITEM MATCHING THE PROVIDED ID
    public function GetItem($TaskId):Task{

        $FileteredArray = array_filter($this->Tasks,  function ($e) use (&$TaskId) {
            return $e->UID== $TaskId;
        });
       
        return reset($FileteredArray);
    
    }

    //RETRIEVES THE LIST OF TASKS TO RUN AT A GIVEN DATE/TIME
    public function GetTasksToRun ($RunAt):array{
        $Retval = array();
        $TimeInfo = TimeInfo::Load();

        foreach ($this->Tasks as $Task){

            //Check if the Task is enabled first
            if($Task->IsEnabled){
              
                //Check if today is a skipped day
                if (!$Task->IsSkipped($RunAt)){

                    if (in_array(date("N"),$Task->Days)){

                        //Check that the Trigger is set
                        if (isset($Task->Trigger)){

                            //Check if the trigger time is actually now
                            if ($Task->Trigger->MustExecuteAt($RunAt)){
                                array_push($Retval,$Task);
                            }

                        }

                    }
                }
           
            }

        }

        return $Retval;

    }

    //RETRIEVES THE LIST OF TASKS TO RUN AT A GIVEN DATE/TIME
    public function GetActionsToRun ($DateTime):array{
        $Retval = array();
        $Day=date ('N', $DateTime);

        //GO THROUGH THE LIST OF ENABLED TASKS WHERE THE DAY MATCHES THE GIVEN
        foreach ($this->Tasks as $Task){

            if ($Task->IsEnabled && $Task->ContainsDay($Day)){
  
                //GO THROUGH THE ACTIONS FOR EACH INDIVIDUAL TASKS
                foreach ($Task->Actions as $Action){
                    
                    //CHECK IF THE ACTION IS ENABLED
                    if ($Action->IsEnabled){

                        //MAKE SURE THE DAY IS NOT SKIPPED
                        if (!$Action->IsSkipped(date ('Ymd', $DateTime))){

                            //MAKE SURE THE TRIGGER IS SET
                            if (isset($Action->Trigger)){

                                //CHECK IF THE GIVEN TIME IS WHEN THE ACTION IS SUPPOSED TO RUN
                                if ($Action->Trigger->MustExecuteAt($DateTime)){
                                    array_push($Retval,$Action);
                                }
                            }
                        }
                    }
                }
            }
        }

        return $Retval;

    }

    //RETRIEVES THE LIST OF TASKS TO RUN AT A GIVEN DAY
    public function GetTasksOnDate ($Date):array{
        $Retval = array();
        $Day=date ('N', $Date);
  
        //RUN THROUGH ALL ITEMS IN THE DATABASE
        foreach ($this->Tasks as $Task){

            //FIRST MAKE SURE THE TASK IS ENABLED
            if ($Task->IsEnabled){

                //CHECK IF THE ITEM DAYS ARRAY CONTAIN THE SPECIFIED DAY
                if(in_array($Day,$Task->Days)){

                    array_push($Retval,$Task);
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

    //RETRIEVES THE TASK BASED ON A PARTICULAR CHILDREN ACTION
    public function GetTaskFromActionId($ActionId):Task{
        $Retval=null;

        foreach($this->Tasks as $TaskItem){
            $FoundAction =array_filter ($TaskItem->Actions , function ($Item) use ($ActionId) {
                if ($Item->UID==$ActionId) {
                    return true;
                }else{
                    return false;
                }
            });

            if (count($FoundAction)>0){
                $Retval=$TaskItem;
            break;
            }
        }

        if (isset($Retval)){
            return $Retval;
        }else{
            throw new Exception ("Action Id not found in the task collection");
            return null;
        }
    }

    //RETRIEVES THE LIST OF ACTIONS TO RUN AT A GIVEN DAY
    public function GetActionsOnDate($Date):array{
        $Retval=array();
        $Day=$Date->format('N');

        //RUN THROUGH ALL ITEMS IN THE DATABASE
        foreach ($this->Tasks as $Task){

            //FIRST MAKE SURE THE TASK IS ENABLED
            if ($Task->IsEnabled){

                //CHECK IF THE ITEM DAYS ARRAY CONTAIN THE SPECIFIED DAY
                if(in_array($Day,$Task->Days)){

                    //GO THROUGHT THE LIST OF ACTIONS IN THE GIVEN TASK
                    foreach ($Task->Actions as $Action){

                        //CHECK IF THE ACTONS IS ENABLED BEFORE PUSHING IT TO THE RETVAL ARRAY
                        if ($Action->IsEnabled){
                            array_push($Retval,$Action);
                        }
                    }
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

}

/**
 * TASK
 * @package    TASK
 * @author     JEROME MARGET
 */
class Task{
    public $Name;
    public $IsEnabled;
    public $Comment;

    public $Days=array();
    public $Actions=array();

    protected $UID;
    protected $CreatedOn;


    function __construct() {

        $this->CreatedOn=new DateTime('now');
        $this->UID=uniqid();
        $this->IsEnabled=true;

    }

    public function __get ($Property) {

        if ($Property=="CreatedOn"){

            return $this->CreatedOn?? null;
        }

        if ($Property=="UID"){

            return $this->UID?? null;
        }

        //RETURNS A LITTERAL EXPRESSIONOF DAYS WHERE IT RUNS
        if ($Property=="RunsOnLitteral"){
            $DayList=[1=>"Monday",2=>"Tuesday",3=>"Wednesday",4=>"Thursday",5=>"Friday",6=>"Saturday",7=>"Sunday"];
            $retval=null;

            if (count($this->Days)==0){
                $retval='Never';
            }else {
                $result=array();
                foreach ($this->Days as $key =>$value) {
                    array_push($result,$DayList[$value]);
                }

                $retval=join(", ",$result);
            }

            return $retval ?? null;
        }

    }

    public function __set( $key, $value ) {}


    //ADD A NEW ITEM TO THE ACTIONS COLLECTION
    public function AddAction($Action){

        array_push($this->Actions,$Action);

    }

    public function ReplaceAction($NewActionItem,$ActionId){
        $oldActionItem= $this->GetAction($ActionId);

        foreach ($this->Actions as $key=>$Value){

            if($Value->UID==$ActionId){
                $this->Actions[$key]=$NewActionItem;
            break;
            }
        }




    }

    //CHECK IF THE GIVEN DAY IS PART OF THE DAYS COLLECTION
    public function ContainsDay(int $DayNumber):bool{
      
        if(in_array($DayNumber,$this->Days)){
            return true;
        } else{
            return false;
        }
    }

    //REMOVES AN ACTION ITEM FROM THE ACTIONS COLLECTION
    public function RemoveAction($ActionId){

        $this->Actions=array_filter($this->Actions,  function ($e) use (&$ActionId) {
            return $e->UID!= $ActionId;
        });

    }
    
    //DUPLICATES A SPECIFIC ACTION
    public function DuplicateAction($ActionId){
        $newAction=null;
        $FileteredArray = array_filter($this->Actions,  function ($e) use (&$ActionId) {
            return $e->UID== $ActionId;
        });

        $newAction=reset($FileteredArray)->Clone();

        $this->AddAction($newAction);
       
        return $newAction;

    }

    //RETRIEVES A SPECIFIC ACTION
    public function GetAction($ActionId){
        $FileteredArray = array_filter($this->Actions,  function ($e) use (&$ActionId) {
            return $e->UID== $ActionId;
        });

        return reset($FileteredArray);
    
    }

    //CALL THIS TO GET A DUPLICATE OF THE CURRENT INSTANCE WITH UPDATED CREATION DATE AND UID
    Public function Clone():Task{
        $Retval = clone $this;
        $Retval->CreatedOn=new DateTime('now');
        $Retval->UID=uniqid();
        $Retval->Name= $Retval->Name . '_Copy';
        return $Retval;
 
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
    const SWITCHON = 2;
    const SWITCHOFF = 3;
    const SETVALUE = 4;

    public $DeviceNames=array();
    public $DeviceLinks=array();
    public $SkippedDays=array();

    public $Name="";

    public $IsEnabled=True;

    protected $UID;
    protected $CreatedOn;
    public $Trigger;

    public function IsSkipped($Day):bool{
        $DayString=null;

        //DAY CAR EITHER BE OF DateTime TYPE OR A SIMPLE Ymd STRING
        if ($Day instanceof DateTime) {
            $DayString=$Day->format('Ymd');
        }else {
            $DayString=$Day;
        }


        if (in_array($DayString,$this->SkippedDays)){
            return true;
        }else{
            return false;
        }
        
    }

    public function Skip($Day):bool{
        $Retval = false;
       
        if (!$this->IsSkipped($Day)){
            array_push ($this->SkippedDays, $Day);
            $Retval=true;
        }
        return $Retval;

    }

    public function RemoveSkipped($Day):bool{
        $Retval = false;
       
        if ($this->IsSkipped($Day)){
            $this->SkippedDays =  array_filter ($this->SkippedDays, function ($DayItem) use ($Day) {
                if ($DayItem!=$Day){
                    return true;
                }else {
                    return false;
                }
            });

            $Retval=true;
        }
        return $Retval;

    }

    public static function GetActionTypes(){
        return array(Action::OPEN=>'Open',
                     Action::CLOSE=>'Close',
                     Action::SWITCHON=>'Switch On',
                     Action::SWITCHOFF=>'Switch Off',
                     Action::SETVALUE=>"Set value (%)"
                    );
    }

    public static function GetActionType($ActionNumber){
        switch ($ActionNumber){
            case 0: return ActionTypes::OPEN;
            case 1: return ActionTypes::CLOSE;
            case 2: return ActionTypes::SWITCHON;
            case 3: return ActionTypes::SWITCHOFF;
            case 4: return ActionTypes::SETVALUE;

        }
    }

    public static function CreateNew($ActionType,$Devices){
        $Retval;
        switch ($ActionType){
            case Action::OPEN: $Retval=new ActionOPEN;
            break;
            case Action::CLOSE: $Retval=new ActionCLOSE;
            break;
            case Action::SWITCHON: $Retval=new ActionSWITCHON;
            break;
            case Action::SWITCHOFF: $Retval=new ActionSWITCHOFF;
            break;
            case Action::SETVALUE: $Retval=new ActionSETVALUE;
            break;
        }

        if (isset ($Devices)){
            $Retval->DeviceNames = $Devices;
        }
     
        return $Retval;
    }


 
    abstract protected function RunAction();
    abstract protected function GetActionString();

    function __construct() {
        $this->CreatedOn=new DateTime('now');
        $this->UID=uniqid();
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


    //CALL THIS TO GET A DUPLICATE OF THE CURRENT INSTANCE WITH UPDATED CREATION DATE AND UID
    public function Clone():Action{
        $Retval = clone $this;
        $Retval->CreatedOn=new DateTime('now');
        $Retval->UID=uniqid();
        $Retval->Name = $Retval->Name . '(copy)';

        return $Retval;
    
    }


    //CALL THI STO CHECK IF A GIVEN DEVICE IS PART OF THE DEVICE COLLECTION
    public function ContainsDevice($DeviceName):bool{

        if (isset($this->DeviceNames) && in_array($DeviceName,$this->DeviceNames)){
            return true;
        }else{
            return false;
        }
    }
    
}

class ActionOPEN extends Action {

    public function RunAction(){

        foreach($this->DeviceLinks as $DeviceLink){
            Action::sendPostCommand($DeviceLink,"UP");
        }

    }

    public function GetActionString():string{
        $DeviceCount= count($this->DeviceNames);
        return 'Open ' . $DeviceCount . ' devices.';
    }

    public function __get ($Property) {

        if ($Property=="ActionType"){
            return Action::OPEN ?? null;
        } 
        
        if ($Property=="UID"){
            return $this->UID ?? null;
        }   
    }

}

class ActionCLOSE extends Action {

    public function RunAction(){
        foreach($this->DeviceLinks as $DeviceLink){
            Action::sendPostCommand($DeviceLink,"DOWN");
        }
    }

    public function GetActionString():string{
        $DeviceCount= count($this->DeviceNames);
        return 'Close ' . $DeviceCount . ' devices.';
    }

    public function __get ($Property) {

        if ($Property=="ActionType"){
            return Action::CLOSE ?? null;
        }   

        if ($Property=="UID"){
            return $this->UID ?? null;
        }   

    }
}

class ActionSWITCHON extends Action {

    public function RunAction(){
        foreach($this->DeviceLinks as $DeviceLink){
            Action::sendPostCommand($DeviceLink,"ON");
        }
    }

    public function GetActionString():string{
        $DeviceCount= count($this->DeviceNames);
        return 'Switch ' . $DeviceCount . ' devices on.';
    }


    public function __get ($Property) {

        if ($Property=="ActionType"){
            return Action::SWITCHON ?? null;
        }   

        if ($Property=="UID"){
            return $this->UID ?? null;
        }   
    }
}

class ActionSWITCHOFF extends Action {

    public function RunAction(){
        foreach($this->DeviceLinks as $DeviceLink){
            Action::sendPostCommand($DeviceLink,"OFF");
        }
    }

    public function GetActionString():string{
        $DeviceCount= count($this->DeviceNames);
        return 'Switch ' . $DeviceCount . ' devices off.';
    }

    public function __get ($Property) {

        if ($Property=="ActionType"){
            return Action::SWITCHOFF ?? null;
        }   

        if ($Property=="UID"){
            return $this->UID ?? null;
        }   
    }
}

class ActionSETVALUE extends Action {
    protected $Value;


    public function __set( $key, $value ){
        if ($key=="Value"){
           $this-> SetActionValue($value);

        }

    }

    public function GetActionString():string{
        $DeviceCount= count($this->DeviceNames);
        return 'Set ' . $this->Value . '% to ' . $DeviceCount . ' devices.';
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

        if ($Property=="UID"){
            return $this->UID ?? null;
        }   
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

    public function getLitteralName(){
        $this->name();

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

        if ($Property=="Info"){
            return $this->FormattedTime ?? null;
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
            
            $tmeInfo=StorageManager::LoadTimeInfo();

            $xTime = mktime(intval($tmeInfo->SunriseHour),intval($tmeInfo->SunriseMinute));

            $strToTimeString=$this->OffsetDirection . intval($this->OffsetHour) . ' hours, ' . $this->OffsetDirection . intval($this->OffsetMinute) . ' minutes' ;

            $uTime= strtotime($strToTimeString,$xTime);

            $temp=date('H:i',$uTime);
            
            return $uTime;

        }

        if ($Property=="FormattedTime"){
            return date("H:i",$this->Time) ?? null;
        }

        if ($Property=="Info"){
            $Retval = "Sunrise ";

            if ($this->OffsetHour + $this->OffsetMinute > 0 ) {
                $Retval .= $this->OffsetDirection;
                $date = new DateTime();
                $date->setTime($this->OffsetHour,$this->OffsetMinute);

                $Retval .=$date->format('H:i');
            } 


            return $Retval;
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
            $tmeInfo= StorageManager::LoadTimeInfo();
           

            $xTime = mktime(intval($tmeInfo->SunsetHour),intval($tmeInfo->SunsetMinute));

            $strToTimeString=$this->OffsetDirection . intval($this->OffsetHour) . ' hours, ' . $this->OffsetDirection . intval($this->OffsetMinute) . ' minutes' ;

            $uTime= strtotime($strToTimeString,$xTime);

            $temp=date('H:i',$uTime);
            
            return $uTime;

           
        }

        if ($Property=="FormattedTime"){
            return date("H:i",$this->Time) ?? null;
        }

        if ($Property=="Info"){
            $Retval = "Sunset ";

            if ($this->OffsetHour + $this->OffsetMinute > 0 ) {
                $Retval .= $this->OffsetDirection;
                $date = new DateTime();
                $date->setTime($this->OffsetHour,$this->OffsetMinute);

                $Retval .=$date->format('H:i');
            } 


            return $Retval;
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

?>