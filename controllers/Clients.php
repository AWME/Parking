<?php namespace AWME\Parking\Controllers;

use Flash;
use Request;
use BackendMenu;
use ValidationException;
use BackendAuth;
use Backend\Classes\Controller;

use AWME\Parking\Models\Parking;
use AWME\Parking\Models\Client;
use AWME\Parking\Models\Garage;
use AWME\Parking\Models\Till;

use AWME\Parking\Classes\Invoice;
use AWME\Parking\Classes\CashRegister;
use AWME\Parking\Classes\Checkout;
use AWME\Parking\Classes\Calculator as Calc;

/**
 * Clients Back-end Controller
 */
class Clients extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    protected $assetsPath = '/plugins/awme/parking/assets';
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('AWME.Parking', 'parking', 'clients');
        
        $this->addJs($this->assetsPath.'/print.js');
    }

    public function update($recordId = null, $context = null)
    {
        /**
         * $Parking & $Client
         * Datos del parking tiket, y Cliente
         * @var array attrs.
         */
        $Client = Client::find($recordId);

        /**
         * $total_price
         * monto a abonar
         * @var int
         */
        $Invoice = new Invoice;
        //Attributes to partial
        $this->vars['total']    = Calc::discount($Invoice->getPrice($Client->billing),$Client->options['discount'],$Client->options['amount']); # Monto a abonar
        $this->vars['discount']        = $Client->options['discount'];
        $this->vars['amount']        = $Client->options['amount'];
        $this->vars['Client']  = $Client;       # Datos del cliente

        $this->asExtension('FormController')->update($recordId, $context);
    }

     public function onCheckout($recordId = null, $context = null)
    {
        /**
         * Validar si la caja esta abierta,
         * antes de crear una venta.
         * @return [type] [description]
         */
        if (!CashRegister::is_open()) {
            throw new ValidationException([
               'please_opening_cash_register' => trans('awme.parking::lang.sales.please_opening_cash_register')
            ]);
        }

        /**
         * $Parking & $Client
         * Datos del parking tiket, y Cliente
         * @var array attrs.
         */
        $Client = Client::find($recordId);

        /**
         * $total_price
         * monto a abonar
         * @var int
         */
        $Invoice = new Invoice;
        //Attributes to partial
        $this->vars['total']    = Calc::discount($Invoice->getPrice($Client->billing),$Client->options['discount'],$Client->options['amount']); # Monto a abonar
        $this->vars['discount']        = $Client->options['discount'];
        $this->vars['amount']        = $Client->options['amount'];
        $this->vars['Client']  = $Client;       # Datos del cliente


        $Client->expiration = Request::input('Client.expiration');
        $Client->options = Request::input('Client.options');
        $Client->save();
        /**
         * $Till
         * Nuevo tiket en caja.
         * @var Till
         */
        $Till = new Till;
        $Till->action = 'sale_subs';
        $Till->seller = BackendAuth::getUser()->first_name;
        $Till->billing = $Client->billing;
        $Till->subtotal = $Invoice->getPrice($Client->billing);
        $Till->total = Calc::discount($Invoice->getPrice($Client->billing),$Client->options['discount'],$Client->options['amount']); # Monto a abonar
        $Till->save();
        
        Flash::success('Se ha modificado la fecha de vencimiento de pago con éxito a '.Request::input('Client.expiration'));

        $this->asExtension('FormController')->update($recordId, $context);
    }
}