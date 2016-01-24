<?php namespace AWME\Parking\Controllers;

use BackendMenu;
use Backend\Classes\Controller;

/**
 * Garages Back-end Controller
 */
class Garages extends Controller
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

        BackendMenu::setContext('AWME.Parking', 'parking', 'garages');
    }

    public function index()
    {
        //
        // Do any custom code here
        //

        // Call the ListController behavior index() method
        $this->asExtension('ListController')->index();
    }
}