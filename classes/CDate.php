<?php

/**
* Date Manipulation Class
* Explanation: purpose of this class is manipulating variuos date function
*/

class CCDate{
    //Variable Decleration
            var $Date;
            var $Month;
            var $Day;
            var $Year;
            var $DateSeperator;
           
    // Constructor: Separates date into Year Month Day 
    public function __construct($date,$strSeperator)
    {
               $this->Date = $date;
               $this->DateSeperator = $strSeperator;
               $date_ar = preg_split('/' . preg_quote($this->DateSeperator) . '/u', $this->Date,3);
               $this->Year = intval($date_ar[0]);
               $this->Month = intval($date_ar[1]);
               $this->Day = intval($date_ar[2]);
               
    }

    // Function to compare 2 dates, target date is passed as a parameter and the start date is used to call this function
    public function compareDates($targetdate)
    {
        $validate_status = $this->validateDate();
        if($validate_status==0) {
        	//echo "<br> Invalid start date";	[TODO: msg needs to populate based on localization]
            return(-10);
       }
       $validate_status = $targetdate->validateDate();
        if($validate_status==0) {
        	//echo "<br> Invalid end date";	[TODO: msg needs to populate based on localization]
            return(-10);
       }
       //compare years
       if($this->Year!=$targetdate->Year) {
               if($this->Year>$targetdate->Year) {
             		//echo "<br>from year is greater than to year";	[TODO: msg needs to populate based on localization]
                	return -1;
            }
            else if($this->Year<$targetdate->Year) {
                //echo "<br>to year is greater than from year";	[TODO: msg needs to populate based on localization]
                return 1;
            }
            else{
                //echo "<br>could not identify the years";	[TODO: msg needs to populate based on localization]
                return -10;
            }
       }
       if($this->Month==$targetdate->Month) {
               if($this->Day == $targetdate->Day) {
                //echo "<br>Dates are the same";	[TODO: msg needs to populate based on localization]
                return 0;
            }
            else if($this->Day > $targetdate->Day) {
               	//echo "<br>From Day is greater than to day";	[TODO: msg needs to populate based on localization]
                return -1;
            }
            else if($this->Day < $targetdate->Day) {
                //echo "<br>To day is greater than from day";	[TODO: msg needs to populate based on localization]
                return 1;
            }
            else {
                //echo "<br>Could not identify the days";	[TODO: msg needs to populate based on localization]
                return 0;
            }
       }
       else {
               if($this->Month>$targetdate->Month) {
                //echo "<br>From month is greater than to month";	[TODO: msg needs to populate based on localization]
                return -1;
            }
            if($this->Month<$targetdate->Month) {
                //echo "<br>To month is greater than from month";	[TODO: msg needs to populate based on localization]
                return 1;
            }
       }
       return 1;
    }

   	// To check if the date passed is correct
   	public function validateDate()
   	{
        if(($this->Month<1)||($this->Month>12)) {
            //echo "<br> Invalid Month";	[TODO: msg needs to populate based on localization]
            return 0;
        }
        // doing the math using K Maps for calculating whether it is a leap year or not, I got the following formula
        // A B` C`  +  A B C
        // A - divisible by 4; B - divisible by 100; C - divisible by 400
        $leapday = 0;
        $A = (($this->Year%4)==0)?1:0;
        $B = (($this->Year%100)==0)?1:0;
        $C = (($this->Year%400)==0)?1:0;
        $R = ($A && (!($B)) && (!($C))) || ($A && $B && $C);
        
        //verifying the day
        //months with 31 days
        $month31 = (($this->Month==1)||($this->Month==3)||($this->Month==5)||($this->Month==7)||($this->Month==8)||($this->Month==10)||($this->Month==12))?1:0;
        
        if(($R && ( ($this->Month==2) && (($this->Day<1) || ($this->Day>29)) ))||(!$R && (($this->Month==2) && (($this->Day<1) || ($this->Day>28))))) {
            //echo "<br> Invalid Day";	[TODO: msg needs to populate based on localization]
            return 0;
        }
        else
        if( ($month31 && ( ($this->Day<1) || ($this->Day>31) ) ) || (!$month31 && ( ($this->Day<1) || ($this->Day>30) ) ) ) {
            //echo "<br> Invalid Day";	[TODO: msg needs to populate based on localization]
            return 0;
        }
        return 1;
   	}
    

    public function display()
    {
        echo "$this->Month-$this->Day-$this->Year";
    }
}
