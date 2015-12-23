<?php

namespace Test\Unit\Legacy\Database\Model;

use Test\Support\UnitTestCase;
use Legacy\Database\Model\Todo;

class TodoTest extends UnitTestCase
{

    public function testInheritsModel()
    {
        $todo = new Todo();
        $this->assertInstanceOf('Legacy\Database\Model', $todo);
    }

    public function testMagicHappens()
    {
        $this->fail('Magic did not happen!');
    }

}
