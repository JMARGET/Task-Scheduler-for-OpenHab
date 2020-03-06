<?php


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



?>