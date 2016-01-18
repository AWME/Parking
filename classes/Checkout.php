<?php namespace AWME\Parking\Classes;

use AWME\Parking\Models\Client;
use AWME\Parking\Models\Settings as Setting;

class Checkout{

    public static function total($decimal_time, $Client)
    {

        if($Client){
            switch (@$Client->billing) {
                case 'Hora':
                    $current_price = $decimal_time * Setting::get('hours_price');
                    return number_format(($current_price < Setting::get('min_price')) ? Setting::get('min_price') : $current_price , 2);
                break;

                case 'Semanal':
                    return number_format(Setting::get('week_price'), 2);
                break;
                
                case 'Mensual':
                    return number_format(Setting::get('month_price'), 2);
                break;

                default:
                    return 0;
                break;
            }
        }else return 0;
    }
}
