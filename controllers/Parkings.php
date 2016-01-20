<?php namespace AWME\Parking\Controllers;

use Flash;
use Request;
use BackendMenu;
use Backend\Classes\Controller;

use AWME\Parking\Models\Parking;
use AWME\Parking\Models\Client;

use AWME\Parking\Classes\Checkout;
use AWME\Parking\Classes\Calculator as Calc;

/**
 * Parkings Back-end Controller
 */
class Parkings extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    protected $assetsPath = '/plugins/awme/parking/assets';
    
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('AWME.Parking', 'parking', 'parkings');

        $this->addJs($this->assetsPath.'/print.js');

    }

    public function update($recordId = null, $context = null)
    {
        /**
         * $Parking
         * $Client
         * @var array attributes
         */
        $Parking = Parking::find($recordId);
        $Client = Client::find($Parking->client_id);

        /**
         * $start_time      # Hora de ingreso
         * $end_time        # Hora de retiro
         * @var timestamp
         */
        $start_time = $Parking->checkin;
        $end_time   = date('Y-m-d H:i:s',time());
        
        /**
         * $time
         * @var array time, details.
         */
        $time = Calc::currentTime($start_time, $end_time);

        //Attributes to partial
        $this->vars['checkin']  = $start_time;
        $this->vars['checkout'] = $end_time;
        $this->vars['time']     = $time['time'];
        $this->vars['total']    = Checkout::total($time['decimal_time'], $Client);
        $this->vars['Parking']  = $Parking;
        $this->vars['Client']  = $Client;

        $this->asExtension('FormController')->update($recordId, $context);

    }

    public function onRefresh($recordId = null, $context = null)
    {
        
        /**
         * $Parking
         * $Client
         * @var array attributes
         */
        $Parking = Parking::find($recordId);
        $Client = Client::find($Parking->client_id);

        /**
         * $start_time      # Hora de ingreso
         * $end_time        # Hora de retiro
         * @var timestamp
         */
        $start_time = $Parking->checkin;
        $end_time   = date('Y-m-d H:i:s',time());
        
        /**
         * $time
         * @var array time, details.
         */
        $time = Calc::currentTime($start_time, $end_time);

        //Attributes to partial
        $this->vars['checkin']  = $start_time;
        $this->vars['checkout'] = $end_time;
        $this->vars['time']     = $time['time'];
        $this->vars['total']    = Checkout::total($time['decimal_time'], $Client);
        $this->vars['Parking']  = $Parking;
        $this->vars['Client']  = $Client;

        $this->asExtension('FormController')->update($recordId, $context);

        
        return Flash::success('Tiempo transcurrido: '. $this->vars['time'].' Con un Total a abonar de: $'.$this->vars['total']);
    }

    public function onCheckout($recordId = null, $context = null)
    {
        
        /**
         * $Parking
         * $Client
         * @var array attributes
         */
        $Parking = Parking::find($recordId);
        $Client = Client::find($Parking->client_id);

        /**
         * $start_time      # Hora de ingreso
         * $end_time        # Hora de retiro
         * @var timestamp
         */
        $start_time = $Parking->checkin;
        $end_time   = date('Y-m-d H:i:s',time());

        /**
         * $time
         * @var array time, details.
         */
        $time = Calc::currentTime($start_time, $end_time);

        //Attributes to partial
        $this->vars['checkin']  = $start_time;
        $this->vars['checkout'] = $end_time;
        $this->vars['time']     = $time['time'];
        $this->vars['total']    = Checkout::total($time['decimal_time'], $Client);
        $this->vars['Parking']  = $Parking;
        $this->vars['Client']  = $Client;
        

        //CHECKOUT
        $Parking->checkout  = $end_time;
        $Parking->status    = 'Cerrado';
        $Parking->total     = $this->vars['total'];
        $Parking->save();
        

        $this->asExtension('FormController')->update($recordId, $context);

        
        return Flash::success('Tiempo transcurrido: '. $this->vars['time'].' Con un Total a abonar de: $'.$this->vars['total']);
    }

    public function test(){

        return phpinfo();
    }
}