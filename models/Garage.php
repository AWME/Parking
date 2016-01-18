<?php namespace AWME\Parking\Models;

use Model;

/**
 * Garage Model
 */
class Garage extends Model
{

    use \October\Rain\Database\Traits\Validation;
    
    /**
     * @var string The database table used by the model.
     */
    public $table = 'awme_parking_garages';
    
    /**
     * @var array Validation rules
     */
    protected $rules = [
        'name' => [
            'required',
            'unique:awme_parking_garages'
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

}