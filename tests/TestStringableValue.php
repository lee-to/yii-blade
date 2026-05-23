<?php

declare(strict_types=1);

namespace Leeto\YiiBlade\Tests;

final readonly class TestStringableValue
{
    public function __construct(
        public string $value,
    ) {}
}
