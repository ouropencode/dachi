<?php

namespace Dachi\Tests;

use Dachi\Core\Modules;

class ModulesTest extends Dachi_TestBase
{
    public function testGetModule()
    {
        $this->assertInstanceOf("\Dachi\Core\Module", Modules::get('UnitTestModuleA'));
        $this->assertInstanceOf("\Dachi\Core\Module", Modules::get('UnitTestModuleB'));
        $this->assertInstanceOf("\Dachi\Core\Module", Modules::get('UnitTestModuleC'));
    }

    public function testGetAllModules()
    {
        $modules = Modules::getAll();
        $this->assertArrayHasKey('UnitTestModuleA', $modules);
        $this->assertArrayHasKey('UnitTestModuleB', $modules);
        $this->assertArrayHasKey('UnitTestModuleC', $modules);
    }

    public function testGetModuleShortname()
    {
        $this->assertEquals('UnitTestModuleA', Modules::get('UnitTestModuleA')->getShortName());
        $this->assertEquals('UnitTestModuleB', Modules::get('UnitTestModuleB')->getShortName());
        $this->assertEquals('UnitTestModuleC', Modules::get('UnitTestModuleC')->getShortName());
    }

    public function testGetModulePath()
    {
        $this->assertEquals('/tmp/fakepath/a', Modules::get('UnitTestModuleA')->getPath());
        $this->assertEquals('/tmp/fakepath/b', Modules::get('UnitTestModuleB')->getPath());
        $this->assertEquals('/tmp/fakepath/c', Modules::get('UnitTestModuleC')->getPath());
    }

    public function testGetModuleNamespace()
    {
        $this->assertEquals('UnitTestNamespace\\UnitTestModuleA', Modules::get('UnitTestModuleA')->getNamespace());
        $this->assertEquals('UnitTestNamespace\\UnitTestModuleB', Modules::get('UnitTestModuleB')->getNamespace());
        $this->assertEquals('UnitTestNamespace\\UnitTestModuleC', Modules::get('UnitTestModuleC')->getNamespace());
    }
}
