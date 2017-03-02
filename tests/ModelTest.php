<?php

namespace Dachi\Tests;

use Dachi\Core\Model;

class ModelTest extends Dachi_TestBase
{
    public function testModelCreation()
    {
        $model = new UnitTestModel();
        $this->assertInstanceOf('\Dachi\Core\Model', $model);
    }

    public function testModelJsonSerialization()
    {
        $a = 123;
        $b = 'abc';
        $c = new \DateTime('now');
        $model = new UnitTestModel();
        $model->initTest($a, $b, $c);

        $this->assertEquals(json_encode([
            'test_var1' => $a,
            'test_var2' => $b,
            'test_var3' => $c->getTimestamp(),
        ]), json_encode($model));
    }
}

class UnitTestModel extends Model
{
    public $test_var1 = null;
    public $test_var2 = null;
    public $test_var3 = null;

    public function initTest($a, $b, $c)
    {
        $this->test_var1 = $a;
        $this->test_var2 = $b;
        $this->test_var3 = $c;
    }
}
