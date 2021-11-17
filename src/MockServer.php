<?php

declare(strict_types=1);

namespace Ciareis\Bypass;

class MockServer
{
    private function __construct(
        private int $port,
    ) {}

    public static function up(int $port): self
    {

    }

    public static function down(int $port)
    {

    }
}
