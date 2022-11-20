<?php

namespace RSO\Collection\Managers;

class CollectionManager
{
    /**
     * Array for storing configuration settings.
     */
    protected $config;
    protected $driver;

    public function __construct( array $config , string $environment) {
        $this->parseConfig($config);

        $driverClass = 'RSO\Collection\Drivers\\'.ucfirst($this->config['driver']).'Collection';

        if (!class_exists($driverClass)) {
            throw new \InvalidArgumentException("Driver [{$this->config['driver']}] is not supported.");
        }
        $this->driver = new $driverClass($environment);
    }

    protected function parseConfig(array $config) {

        $defaults = [
            'base_url'  => "",
            'driver'    => 'directus',
            'token'    => false,
            'verify_client' => true,
            'token_key' => 'Authorization'
        ];
        $this->config = array_merge($defaults, $config);
    }

    public function getCollection() {
        return $this->driver;
    }
}