<?php

namespace Test\Unit\Web\Database\Model;

use Test\Support\UnitTestCase;
use Web\Database\Model\Todo;

class TodoTest extends UnitTestCase
{

    public function testInheritsModel()
    {
        $todo = new Todo();
        $this->assertInstanceOf('Web\Database\Model', $todo);
    }

    public function testMagicHappens()
    {
        $this->fail('Magic did not happen!');
    }

}
