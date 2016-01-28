<?php namespace AWME\Parking\Controllers;

use Flash;
use Backend;
use Redirect;
use Request;
use BackendMenu;
use ValidationException;
use Backend\Classes\Controller;

use AWME\Parking\Classes\CashRegister;

/**
 * Tills Back-end Controller
 */
class Tills extends Controller
{
    public $implement = [
        'Backend.Behaviors.FormController',
        'Backend.Behaviors.ListController'
    ];

    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';

    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('AWME.Parking', 'parking', 'tills');
    }

    public function index()
    {
        $this->vars['cash_register']['is_open'] = CashRegister::is_open();

        // Call the ListController behavior index() method
        $this->asExtension('ListController')->index();
    }

    public function onPrintVersion()
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
         * Button Validation
         */
        if (Request::input('onPrintVersion') != 'printVersion')
            throw new ApplicationException('Invalid value');

        $CashRegister = new CashRegister;
        
        if($CashRegister->is_open()):
            /**
             * Exec open function
             */
            
            $this->vars['cash_register']['is_open'] = CashRegister::is_open();
            $this->vars['till'] = CashRegister::onClosing();
            $this->vars['printVersion'] = true;
            // Call the ListController behavior index() method
            $this->asExtension('ListController')->index();

            Flash::success("Vista de impresión exitosa, presione \"Ctrl + P\" para imprimir, \"F5\" para salir.");
        else: 
            return  Flash::error(trans('awme.parking::lang.tills.already_closed'));
        endif;

    }

    public function onOpenTill()
    {
        /**
         * Button Validation
         */
        if (Request::input('onOpenTill') != 'openTill')
            throw new ApplicationException('Invalid value');

        $CashRegister = new CashRegister;
        
        if(!$CashRegister->is_open()):
            /**
             * Exec open function
             */
            $CashRegister->open();

            Flash::success(trans('awme.parking::lang.tills.opening_successfully'));
            return Redirect::to(Backend::url("awme/parking/tills"));
        else: 
            return  Flash::error(trans('awme.parking::lang.tills.already_opening'));
        endif;

    }

    public function onCloseTill()
    {
        /**
         * Button Validation
         */
        if (Request::input('onCloseTill') != 'closeTill')
            throw new ApplicationException('Invalid value');

        $CashRegister = new CashRegister;
        
        if($CashRegister->is_open()):

            $this->vars['cash_register']['is_open'] = CashRegister::is_open();
            $this->vars['till'] = CashRegister::onClosing();
            // Call the ListController behavior index() method
            $this->asExtension('ListController')->index();
            /**
             * Exec open function
             */
            $CashRegister->close();

            Flash::warning(trans('awme.parking::lang.tills.closed_successfully'));
            return Redirect::to(Backend::url("awme/parking/tills"));
        else: 
            return  Flash::error(trans('awme.parking::lang.tills.already_closed'));
        endif;
    }


    public function onSummaryTill()
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
         * Button Validation
         */
        if (Request::input('onSummaryTill') != 'summaryTill')
            throw new ApplicationException('Invalid value');

        $CashRegister = new CashRegister;
        
        if($CashRegister->is_open()):
            /**
             * Exec open function
             */
            
            $this->vars['cash_register']['is_open'] = CashRegister::is_open();
            $this->vars['till'] = CashRegister::onClosing();
            // Call the ListController behavior index() method
            $this->asExtension('ListController')->index();

            Flash::success(trans('awme.parking::lang.tills.summary_successfully'));
        else: 
            return  Flash::error(trans('awme.parking::lang.tills.already_closed'));
        endif;

    }
}