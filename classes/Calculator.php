<?php namespace AWME\Parking\Classes;

class Calculator{

    public static function format($n)
    {
        return number_format($n, 2, '.', '');
    }

    /**
     * Suma
     */
    public static function suma($n)
    {
        return array_sum($n);
    }

    /**
     * Suma
     */
    public static function resta($a,$b)
    {
        return (array_sum($a) - array_sum($b));
    }

    public static function percent($a,$b){

        return ($a * $b)/100;
    }
    /**
     * Multiply
     */
    public static function multiply($a, $b)
    {
        return ($a * $b);
    }

    public static function test()
    {
        return "prueba";
    }

    /**
     * [currentTime]
     * @param  timestamp $start_time    # Hora de ingreso
     * @param  timestamp $end_time      # Hora de salida
     * @return array                    # Tiempo transcurrido
     */
    public static function currentTime($start_time, $end_time){

        $total_seconds = strtotime($end_time) - strtotime($start_time); 
        $hours              = floor ( $total_seconds / 3600 );
        $minutes            = ( ( $total_seconds / 60 ) % 60 );
        $seconds            = ( $total_seconds % 60 );
        
        $time = [];
        $time['hours']      = str_pad( $hours, 2, "0", STR_PAD_LEFT );
        $time['minutes']    = str_pad( $minutes, 2, "0", STR_PAD_LEFT );
        $time['seconds']    = str_pad( $seconds, 2, "0", STR_PAD_LEFT );
         
        $time['total_time']       = implode( ':', $time );

        /**
         * Hora decimal.
         * ej: 1.25, 2.5, 3.75
         */
        $time['decimal_time'] = number_format($time['hours']+($time['minutes']/60), 2);

        $decimals = explode('.', $time['decimal_time']);
        $decimal_hours = current($decimals);
        $decimal_mins = end($decimals);

        $time['decimal_time'] = $decimal_hours.'.'.$decimal_mins;

        if($decimal_mins >= 50){
            $time['decimal_time'] = ($decimal_hours + 1).'.00';
        }else if($decimal_mins <= 50){
            $time['decimal_time'] = $decimal_hours.'.50';
        }


        return $time;
    }
}