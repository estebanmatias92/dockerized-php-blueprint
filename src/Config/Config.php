<?php
namespace {{ placeholder.namespace }}\Config;

/*
    Config entity
*/
class Config
{
    protected $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    public function get($key, $default = null)
    {
        return $this->settings[$key] ?? $default;
    }
}
