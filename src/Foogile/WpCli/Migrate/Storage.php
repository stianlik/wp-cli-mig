<?php

namespace Foogile\WpCli\Migrate;

class Storage
{
    public function get($name, $default = '')
    {
        return get_option($name, $default);
    }
    
    public function update($name, $value)
    {
        return update_option($name, $value);
    }
}
