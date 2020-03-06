
var TASKFORM= {

    //GLOBAL CALL TO VALIDATE THE FORM
    ValidateTaskName: function () {
        var retval = true;
        try {
            
            //LOAD THE FORM
            var thisForm=document.forms["frmtask"];

            //LOAD THE NAME CONTROL
            var name = thisForm["taskname"];
            
            if (!this.ValidateWithRegex(name,'^(?!\\s)+.')){
                retval=false;
            }

        } catch (e){
            alert(e);
        }

        return retval;
    },

    //GLOBAL CALL TO VALIDATE THE FORM
    ValidateTriggerOLD: function () {
        
        var retval = true;

        try {

            var thisForm=document.forms["taskform"];

            //VALIDATE THE NAME
            var name = thisForm["actionname"];
            
            if (!this.ValidateWithRegex(name,'^(?!\\s)+.')){
                retval=false;
            }


            //VALIDATE HOURS OR OFFSET DEPENDING ON THE CHOOSEN METHOD
            var triggerType = thisForm["triggerType"];


            switch (parseInt(triggerType.value)) {
                case 0:

                    //LOADS THE FORMS BASED ON NAME
                    var iHour = thisForm["hour"];
                    var iMinute = thisForm["minute"];

                    //VALIDATE HOURS
                    if (!this.ValidateWithRegex(iHour,'^(?:2[0-3]|[01][0-9]|[0-9])$')){
                        retval=false;
                    }

                    //VALIDATE MINUTES
                    if (!this.ValidateWithRegex(iMinute,'^([0-5][0-9]|[0-9])$')){
                        retval=false;
                    }

                    break;

                case 1:
                    //SAME CASE AS BELOW
                case 2:
                    //LOADS THE FORMS BASED ON NAME
                    var iHour = thisForm["offsethour"];
                    var iMinute = thisForm["offsetminute"];

                    //VALIDATE OFFSET HOURS
                    if (!this.ValidateWithRegex(iHour,'^(?:2[0-3]|[01][0-9]|[0-9]|)$')){
                        retval=false;
                    }

                    //VALIDATE OFFSET MINUTES
                    if (!this.ValidateWithRegex(iMinute,'^([0-5][0-9]|[0-9]|)$')){
                        retval=false;
                    }
                    break;
            }


            
            //VALIDATE ACTIONS ON THE CHOOSEN METHOD
            var actionType = thisForm["actionType"];

            if (actionType.value == "-") {
                actionType.classList.add("is-invalid");
                retval=false;
            }else {
                actionType.classList.remove("is-invalid");
            }

            //VALIDATE THE SPECIFIED VALUE
            if (actionType.value == 4) {
                var iSpecifiedValue = thisForm["setdevicevalue"];
                
                //VALIDATE SPECIFIED VALUE
                if (!this.ValidateWithRegex(iSpecifiedValue,'^([0-9]|[0-9][0-9]|100)$')){
                    retval=false;
                }
            }


        } catch (e){
            alert(e);
        }

        return retval;


    },

    //GLOBAL CALL TO VALIDATE THE FORM
    ValidateTrigger: function (IdExtention) {
        var retval = true;
        
        try {

            var suffix="";

            if (IdExtention !=null){
                suffix='_' + IdExtention;
            }


            var thisForm=document.forms["frm" + suffix];

            //VALIDATE THE NAME
            var name = thisForm["actionname" + suffix];

            
            if (!this.ValidateWithRegex(name,'^(?!\\s)+.')){
                retval=false;
            }

            //VALIDATE HOURS OR OFFSET DEPENDING ON THE CHOOSEN METHOD
            var triggerType = thisForm["triggerType" + suffix];


            switch (parseInt(triggerType.value)) {
                case 0:

                    //LOADS THE FORMS BASED ON NAME
                    var iHour = thisForm["hour" + suffix];
                    var iMinute = thisForm["minute" + suffix];

                    //VALIDATE HOURS
                    if (!this.ValidateWithRegex(iHour,'^(?:2[0-3]|[01][0-9]|[0-9])$')){
                        retval=false;
                    }

                    //VALIDATE MINUTES
                    if (!this.ValidateWithRegex(iMinute,'^([0-5][0-9]|[0-9])$')){
                        retval=false;
                    }

                    break;

                case 1:
                    //SAME CASE AS BELOW
                case 2:
                    //LOADS THE FORMS BASED ON NAME
                    var iHour = thisForm["offsethour" + suffix];
                    var iMinute = thisForm["offsetminute" + suffix];

                    //VALIDATE OFFSET HOURS
                    if (!this.ValidateWithRegex(iHour,'^(?:2[0-3]|[01][0-9]|[0-9]|)$')){
                        retval=false;
                    }

                    //VALIDATE OFFSET MINUTES
                    if (!this.ValidateWithRegex(iMinute,'^([0-5][0-9]|[0-9]|)$')){
                        retval=false;
                    }
                    break;
            }


            //VALIDATE ACTIONS ON THE CHOOSEN METHOD
            var actionType = thisForm["actionType" + suffix];

            if (actionType.value == "-") {
                actionType.classList.add("is-invalid");
                retval=false;
            }else {
                actionType.classList.remove("is-invalid");
            }

            //VALIDATE THE SPECIFIED VALUE
            if (actionType.value == 4) {
                var iSpecifiedValue = thisForm["setdevicevalue" + suffix];
                
                //VALIDATE SPECIFIED VALUE
                if (!this.ValidateWithRegex(iSpecifiedValue,'^([0-9]|[0-9][0-9]|100)$')){
                    retval=false;
                }
            }


        } catch (e){
            alert(e);
        }

        return retval;


    },

    //VALIDATE A CONTROL BASED ON THE GIVEN REGEX
    ValidateWithRegex: function (control,regexValue){
        var retval = true;
        var pattern = new RegExp(regexValue);

        if (pattern.test(control.value) == false) {
            retval = false;
            control.classList.add("is-invalid");
        }else{
            control.classList.remove("is-invalid");
        }

        return retval;
        
    },

    //SHOW THE APPROPRIATE DIV BASED ON THE SELECTED TRIGGER TIME
    ToogleTriggerProperties: function(IdExtention) {
        try {

            var suffix="";

            if (IdExtention !=null){
                suffix='_' + IdExtention;
            }

            var select = $('#triggerType' + suffix);
            $('#Time' + suffix).hide();
            $('#SunriseSunset' + suffix).hide();

            if (select.val() == 0) {
                $('#Time'+ suffix).show();
            }

            if (select.val() == 1 || select.val() == 2) {
                $('#SunriseSunset'+ suffix).show();
            }
        } catch (e){
            alert(e);
        }
    },

    //SHOW DEVICES BASED ON THE SELECTED ACTION TYPE
    ToogleDevices: function(IdExtention) {
        try {
            
            var suffix="";

            if (IdExtention !=null){
                suffix='_' + IdExtention;
            }

            var select = $('#actionType' + suffix);
            $('#device_rollers' + suffix).hide();
            $('#device_lights' + suffix).hide();
            $('#device_setvalue' + suffix).hide();
            $('#setdevicevalue' + suffix).hide();

            if (select.val() == 0 ||select.val() == 1) {
                $('#device_rollers' + suffix).show();
            }

            if (select.val() == 2 ||select.val() == 3) {
                $('#device_lights' + suffix).show();
            }
        
            if (select.val() == 4) {
                $('#device_setvalue' + suffix).show();
                $('#setdevicevalue' + suffix).show();
            }

        } catch (e){
            alert(e);
        }

    }

}



var DEVICESFORM = {


    //GLOBAL CALL TO VALIDATE THE FORM
    ValidateTrigger: function () {
        
        var _Retval = true;

        try {

            
            //VALIDATE THE URL
            var x = document.forms["RefreshParameters"]["ohserverurl"];
            x.classList.remove("is-invalid");

            if (x.value == "") {
                x.classList.add("is-invalid");
                _Retval = false;
            }
         


        } catch (e){
            alert(e);
        }

        return _Retval;


    }
}