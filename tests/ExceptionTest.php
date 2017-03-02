<?php

namespace Dachi\Tests;

class ExceptionTest extends Dachi_TestBase
{
    public function testDachiException()
    {
        $this->assertInstanceOf('\Dachi\Core\Exception', new \Dachi\Core\Exception());
    }
}
