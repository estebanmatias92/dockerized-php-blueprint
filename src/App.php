<?php
namespace {{ placeholder.namespace }};

use {{ placeholder.namespace }}\Config\Config;
use function {{ placeholder.namespace }}\HelperModule\hello_world;

/*
    Main app object
*/
class App
{
    protected $config;

    /*
        Creates the App and stores configuration through Dependency Injection
    */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /*
        App's entrypoint
    */
    public function init()
    {
        // Get example data from configuration
        $name = $this->config->get('app_name');
        // Print grettings
        hello_world($name);
    }
}


