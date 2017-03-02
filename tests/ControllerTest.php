<?php

namespace Dachi\Tests;

class ControllerTest extends Dachi_TestBase
{
    public function testControllerParentExists()
    {
        $this->assertEquals(true, class_exists('\Dachi\Core\Controller'));
    }
}
