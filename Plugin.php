<?php namespace AWME\Parking;

use Backend;
use System\Classes\PluginBase;

/**
 * Parking Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Parking',
            'description' => 'software de control de Estacionamiento.',
            'author'      => 'AWME, LucasZdv',
            'icon'        => 'icon-car'
        ];
    }

    /**
     * Registers any front-end components implemented in this plugin.
     *
     * @return array
     */
    public function registerComponents()
    {
        return []; // Remove this line to activate

        return [
            'AWME\Parking\Components\MyComponent' => 'myComponent',
        ];
    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'awme.parking.use_parking' => [
                'tab' => 'Parking',
                'label' => 'Usar el software de estacionamiento'
            ],
        ];
    }

    /**
     * Registers back-end navigation items for this plugin.
     *
     * @return array
     */
    public function registerNavigation()
    {
        return [
            'parking' => [
                'label'       => 'Parking',
                'url'         => Backend::url('awme/parking/clients'),
                'icon'        => 'icon-car',
                'permissions' => ['awme.parking.use_parking'],
                'order'       => 500,

                'sideMenu' => [
                    'parkings' => [
                        'label'       => 'Parking',
                        'url'         => Backend::url('awme/parking/parkings'),
                        'icon'        => 'icon-car',
                        'permissions' => ['awme.parking.use_parking'],
                    ],

                    'clients' => [
                        'label'       => 'Clientes',
                        'url'         => Backend::url('awme/parking/clients'),
                        'icon'        => 'icon-users',
                        'permissions' => ['awme.parking.use_parking'],
                    ],

                    'garages' => [
                        'label'       => 'Cocheras',
                        'url'         => Backend::url('awme/parking/garages'),
                        'icon'        => 'icon-sitemap',
                        'permissions' => ['awme.parking.use_parking'],
                    ],
                ],
            ]
        ];
    }

    /**
     * Register Backend Plugin Settings
     * 
     */
    public function registerSettings()
    {
        return [
            'parking_settings'  => [
                'label'       => 'Parking',
                'description' => 'Configuración del Sitema de Parking',
                'category'    => 'Parking',
                'icon'        => 'icon-car',
                'class'       => 'AWME\Parking\Models\Settings',
                'order'       => 410,
                //'permissions' => [ 'awme.octomanage.settings' ],
                'keywords'    => 'parking, car, estacionamiento'
            ]
        ];
    }
}