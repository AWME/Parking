<?php namespace AWME\Parking\Models;

use Model;

/**
 * Client Model
 */
class Client extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'awme_parking_clients';

    /**
     * @var array Validation rules
     */
    protected $rules = [
        'name' => ['required'],
        'lastname' => ['required'],
        'fullname' => [
            'required',
            'unique:awme_parking_clients'
        ],

        'registration' => [
            'required',
            'alpha_dash',
            'unique:awme_parking_clients'
        ],
    ];

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [];

    /**
     * @var array Relations
     */
    public $hasOne = [];
    public $hasMany = [];
    public $belongsTo = [];
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
    public function setFullnameAttribute($value)
    {
        /**
         * Tiket automatico, según numero de id.
         */
        $this->attributes['fullname'] = $this->attributes['name'].' '.$this->attributes['lastname'];
    }

}