<?php

namespace Tests;

use Ciareis\Bypass\Bypass;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public Bypass $bypass;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bypass = Bypass::up();
    }


    protected function tearDown(): void
    {
        $this->bypass->down();

        parent::tearDown();
    }
}
