<?php namespace AWME\Parking\Classes;

use Flash;
use BackendAuth;

use AWME\Parking\Models\Till;
use AWME\Parking\Classes\Calculator as Calc;

/**
* 
*/
class CashRegister
{
    /**
     * -------------------------------------------------------------------
     * Open
     * -------------------------------------------------------------------
     * Abir caja
     * 
     */
    public function open()
    {
        $Till = new Till;
        $Till->action = 'opening_till';
        $Till->seller = BackendAuth::getUser()->first_name;
        $Till->subtotal = 0;
        $Till->total = 0;
        $Till->save();
    }


    public static function getActionTrans($trans)
    {
        return trans('awme.parking::lang.tills.'.$trans);
    }

    /**
     * -------------------------------------------------------------------
     * getLastOpen
     * -------------------------------------------------------------------
     * Obtener datos de la ultima apertura de caja.
     * 
     * @return array $lastOpen
     */
    public static function getLastOpen()
    {
        $lastOpen = Till::where('action', Self::getActionTrans('opening_till'))->orderBy('created_at', 'desc')->first();
        return $lastOpen;
    }

    /**
     * -------------------------------------------------------------------
     * getLastClose
     * -------------------------------------------------------------------
     * Obtener datos del ultimo cierre de caja.
     * 
     * @return array $lastClose
     */
    public static function getLastClose()
    {
        $lastClose = Till::where('action', Self::getActionTrans('closing_till'))->orderBy('created_at', 'desc')->first();
        return $lastClose;
    } 



    public static function getLastSales()
    {
        /**
         * Total de depositos
         * @var decimal
         */
        $lastOpen = Self::getLastOpen()->toArray(); #$lastOpen['created_at']

        $sales = Till::where('action', Self::getActionTrans('sale'))
                        ->where('created_at','>=', $lastOpen['created_at'])->get()->toArray();
        $sales['last_open'] = $lastOpen;
        return $sales;
    }



    /**
     * [is_open description]
     * @return boolean [description]
     */
    public static function is_open()
    {
        $lastOpen = Self::getLastOpen();
        $lastClose = Self::getLastClose();
        
        if(empty($lastOpen)){
            $status = false;
        }else {

            $status = true;

            if(!empty($lastClose))
            {
                if($lastClose->id > $lastOpen->id)
                    $status = false;
            }

        }

        return $status;
    }

    public static function tillOnLastClosing()
    {
        $Tills = Till::where('action',Self::getActionTrans('closing_till'))->orderBy('created_at', 'desc')->first();
        
        if(empty($Tills->till))
            $till = 0.00;
        else $till = $Tills->till;

        return $till;
    }

    public static function onClosing()
    {

        $getLastOpen = Self::getLastOpen()->toArray(); #$lastOpen['created_at']
        $lastOpen = $getLastOpen['created_at'];

        /**
         * Total de depositos
         * @var decimal
         */
        $deposites = Till::where('action', Self::getActionTrans('deposit'))->where('created_at','>=', $lastOpen)->get()->toArray();
        $deposites = array_sum(array_column($deposites, 'total'));
        
        /**
         * Total de Ventas de Parking
         * @var decimal
         */
        $parking_sales = Till::where('action', Self::getActionTrans('sale'))->where('created_at','>=', $lastOpen)->get()->toArray();
        $parking_sales = array_sum(array_column($parking_sales, 'total'));

        /**
         * Total de Abonoso pagos
         * @var decimal
         */
        $total_subs = Till::where('action', Self::getActionTrans('sale_subs'))->where('created_at','>=', $lastOpen)->get()->toArray();
        $total_subs = array_sum(array_column($total_subs, 'total'));
        
        /**
         * Total de Retiros
         * @var decimal
         */
        $withdrawals = Till::where('action', Self::getActionTrans('withdrawal'))->where('created_at','>=', $lastOpen)->get()->toArray();
        $withdrawals = array_sum(array_column($withdrawals, 'total'));

        $till = [
            //'total_in_open_till' => $getLastOpen['till'],     # total de depositos hechos
            'total_deposites'       => $deposites,          # total de depositos hechos
            'total_parking_sales'   => $parking_sales,  # total de ventas de parking
            'total_subscription'    => $total_subs,     # total de ventas de subscripcion
            'total_witdrawls'       => $withdrawals,            # total de retiros de dinero
            'total_all_sales'       => Calc::suma([$parking_sales,$total_subs]),    # total de todas las ventas parking y subscripcion
            'total_income'          => Calc::suma([$parking_sales,$total_subs,$deposites]),     # total de ingreso de dinero, Parking, Subs y Deposito
        ];
        $till['total_in_till']      = Calc::resta([$till['total_income']], [$withdrawals]); # lo que queda en caja con la suma total menos los depositos.

        return (object) $till;
    }

    public function close($summary = null)
    {
        $onClosing = Self::onClosing();

        $Till = new Till;
        $Till->action       = ($summary) ? 'summary' : 'closing_till';
        $Till->seller       = BackendAuth::getUser()->first_name;
        $Till->subtotal     = $onClosing->total_income;
        $Till->total        = $onClosing->total_in_till;
        $Till->save();
    }
}