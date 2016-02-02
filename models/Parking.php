<?php namespace AWME\Parking\Models;

use Model;
use Request;
use ValidationException;

use AWME\Parking\Classes\CashRegister;

use AWME\Parking\Models\Parking;
use AWME\Parking\Models\Client;
use AWME\Parking\Models\Garage;
/**
 * Parking Model
 */
class Parking extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'awme_parking_parkings';

    /**
     * @var array Validation rules
     */
    protected $rules = [
        'tiket' => [
            'between:2,45',
            'unique:awme_parking_parkings'
        ],
        'client' => ['required', 'numeric'],
        'garage' => ['required', 'numeric'],
    ];
    /**
     * @var array List of attributes to purge.
     */
    protected $purgeable = ['discount','amount'];

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    protected $jsonable = ['client_data','options'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'client' => 'AWME\Parking\Models\Client',
        'garage' => ['AWME\Parking\Models\Garage',
                    'scope'      => 'isActive'
                    ],
    ];

    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

    public function inGarage(){

        return Garage::find($this->garage_id);
    }

    /**
    * Status de cochera
    */
    public function beforeCreate()
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
    }

    public function afterCreate()
    {
        /**
         * $Garage - Status
         * @var string # Cambiar el estado de la cochera.
         */
        $Garage = Garage::find($this->garage_id);
        $Garage->status = 'No Disponible';
        $Garage->save();

        if(!$this->options['discount'] || !$this->options['amount']){
            $this->options = ['discount' => 'percent',
                                'amount' => 0];
            $this->save();
        }
    }

    /**
     * Set the Tiket Number
     *
     * @param  string  $value
     * @return string
     */
    public function setTiketAttribute($value)
    {
        /**
         * Tiket automatico, según numero de id.
         */
        if(!$value){
           
            if(!$this->attributes['id']):
                $tiket = Parking::where('id', '>', '0')->orderBy('created_at', 'desc')->first();
                
                if($tiket)
                    $tiket_id = $tiket->id + 1;
                else $tiket_id = 1;
            else: 
                $tiket_id = $this->attributes['id'];
            endif;

            $this->attributes['tiket'] = str_pad(($tiket_id), 6, "0", STR_PAD_LEFT);
        }else $this->attributes['tiket'] = $value;
    }

    /**
     * Set Full name client
     * @param string $value #Nombre completo del cliente
     */
    public function setFullnameAttribute($value)
    {
        $this->attributes['fullname'] = @Client::find(Request::input('Parking.client'))->fullname;
    }

    /**
     * Set Full name client
     * @param string $value #Nombre completo del cliente
     */
    public function setBillingAttribute($value)
    {
        $this->attributes['billing'] = @Client::find(Request::input('Parking.client'))->billing;
    }
    
}