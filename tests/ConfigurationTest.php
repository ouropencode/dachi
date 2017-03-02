<?php

namespace Dachi\Tests;

use Dachi\Core\Configuration;

class ConfigurationTest extends Dachi_TestBase
{
    public function testGetConfiguration()
    {
        $timezone = Configuration::get('dachi.timezone');
        $this->assertEquals('Europe/London', $timezone);
    }

    public function testGetConfigurationDefault()
    {
        $impossible = Configuration::get('impossible_config_entry_'.time());
        $this->assertEquals('default', $impossible);

        $impossible = Configuration::get('impossible_config_entry_'.time(), 'custom_default');
        $this->assertEquals('custom_default', $impossible);
    }
}
