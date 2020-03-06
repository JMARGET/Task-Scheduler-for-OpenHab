<?php


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



    

}

?>