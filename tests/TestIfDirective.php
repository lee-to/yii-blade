<?php

declare(strict_types=1);

namespace Leeto\YiiBlade\Tests;

use Closure;
use Leeto\YiiBlade\DirectiveInterface;
use Leeto\YiiBlade\DirectiveType;

final class TestIfDirective implements DirectiveInterface
{
    public function getType(): DirectiveType
    {
        return DirectiveType::IF;
    }

    public function getName(): string
    {
        return 'testIf';
    }

    public function handler(): Closure
    {
        return static fn (bool $allowed): bool => $allowed;
    }
}
