<?php namespace AWME\Parking\Models;

use Model;
use Request;
use AWME\Parking\Models\Parking;
use AWME\Parking\Models\Client;
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
        'client' => ['required'],
        'garage' => ['required'],
    ];

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    protected $jsonable = ['client_data'];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [
        'client' => 'AWME\Parking\Models\Client',
        'garage' => 'AWME\Parking\Models\Garage',
    ];

    public $belongsToMany = [];
    public $morphTo = [];
    public $morphOne = [];
    public $morphMany = [];
    public $attachOne = [];
    public $attachMany = [];

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
        $this->attributes['fullname'] = Client::find(Request::input('Parking.client'))->fullname;
    }

    /**
     * Set Full name client
     * @param string $value #Nombre completo del cliente
     */
    public function setBillingAttribute($value)
    {
        $this->attributes['billing'] = Client::find(Request::input('Parking.client'))->billing;
    }
    
}