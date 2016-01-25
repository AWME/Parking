<?php namespace AWME\Parking\Controllers;

use Flash;
use Request;
use BackendMenu;
use Backend\Classes\Controller;

use AWME\Parking\Models\Parking;
use AWME\Parking\Models\Client;
use AWME\Parking\Models\Garage;

use AWME\Parking\Classes\Invoice;
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
         * [$Invoice description]
         * @var Invoice
         */
        $Invoice = new Invoice();
        $Invoice->parkingId = $recordId;
        
        $times = $Invoice->getTimes();

        /**
         * $Parking & $Client
         * Datos del parking tiket, y Cliente
         * @var array attrs.
         */
        $Parking = Parking::find($recordId);
        $Client = Client::find($Parking->client_id);

        /**
         * $total_price
         * monto a abonar
         * @var int
         */
        $total_price = Checkout::total($times['decimal_time'], $Client);

        //Attributes to partial
        $this->vars['times']  = $times;         # Tiempos (start_time, end_time, total_time, decimal_time)
        $this->vars['total']    = $total_price; # Monto a abonar
        $this->vars['Parking']  = $Parking;     # Datos del tiket
        $this->vars['Client']  = $Client;       # Datos del cliente

        $this->asExtension('FormController')->update($recordId, $context);
    }

    public function onRefresh($recordId = null, $context = null)
    {
        
        /**
         * [$Invoice description]
         * @var Invoice
         */
        $Invoice = new Invoice();
        $Invoice->parkingId = $recordId;
        
        $times = $Invoice->getTimes();

        /**
         * $Parking & $Client
         * Datos del parking tiket, y Cliente
         * @var array attrs.
         */
        $Parking = Parking::find($recordId);
        $Client = Client::find($Parking->client_id);

        /**
         * $total_price
         * monto a abonar
         * @var int
         */
        $total_price = Checkout::total($times['decimal_time'], $Client);

        //Attributes to partial
        $this->vars['times']  = $times;         # Tiempos (start_time, end_time, total_time, decimal_time)
        $this->vars['total']    = $total_price; # Monto a abonar
        $this->vars['Parking']  = $Parking;     # Datos del tiket
        $this->vars['Client']  = $Client;       # Datos del cliente

        $this->asExtension('FormController')->update($recordId, $context);

        
        return Flash::success('Tiempo transcurrido: '. $times['total_time'].' Con un Total a abonar de: $'.$total_price);
    }

    public function onCheckout($recordId = null, $context = null)
    {
        
        /**
         * [$Invoice description]
         * @var Invoice
         */
        $Invoice = new Invoice();
        $Invoice->parkingId = $recordId;
        
        $times = $Invoice->getTimes();

        /**
         * $Parking & $Client
         * Datos del parking tiket, y Cliente
         * @var array attrs.
         */
        $Parking = Parking::find($recordId);
        $Client = Client::find($Parking->client_id);

        /**
         * $total_price
         * monto a abonar
         * @var int
         */
        $total_price = Checkout::total($times['decimal_time'], $Client);

        //Attributes to partial
        $this->vars['times']  = $times;         # Tiempos (start_time, end_time, total_time, decimal_time)
        $this->vars['total']    = $total_price; # Monto a abonar
        $this->vars['Parking']  = $Parking;     # Datos del tiket
        $this->vars['Client']  = $Client;       # Datos del cliente

        $this->asExtension('FormController')->update($recordId, $context);

        /**
         * $Parking Checkout
         * @var timestamp $checkout #endtime
         * @var string    $status   # tiket cerrado
         * @var int       $total    # total a pagar.
         */
        $Parking->checkout  = $times['end_time'];
        $Parking->status    = 'Cerrado';
        $Parking->total     = $total_price;
        $Parking->save();
        
        /**
         * $Garage
         * @var string    $status # Liberar cochera
         */
        $Garage = Garage::find($Parking->garage_id);
        $Garage->status = 'Disponible';
        $Garage->save();
        
        return Flash::success('Tiempo transcurrido: '.$times['total_time'].' Con un Total a abonar de: $'.$total_price);
    }
}