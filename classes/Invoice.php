<?php namespace AWME\Parking\Classes;

use AWME\Parking\Classes\Calculator as Calc;

use AWME\Parking\Models\Client;
use AWME\Parking\Models\Parking;
use AWME\Parking\Models\Settings as Setting;

class Invoice{

    public $parkingId;

    public function getTimes()
    {
        /**
         * $Parking
         * $Client
         * @var array attributes
         */
        $Parking = Parking::find($this->parkingId);
        $Client = Client::find($Parking->client_id);

        /**
         * $start_time      # Hora de ingreso
         * $end_time        # Hora de retiro
         * @var timestamp
         */
        $start_time = $Parking->checkin;
        $end_time   = date('Y-m-d H:i:s',time());

        $currents = Calc::currentTime($start_time, $end_time);

        return $times = ['start_time'   => $start_time,
                        'end_time'      => $end_time,
                        'total_time'    => $currents['total_time'],
                        'decimal_time'  => $currents['decimal_time'],
                     ];
    }
}