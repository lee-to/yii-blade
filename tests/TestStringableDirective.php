<?php

declare(strict_types=1);

namespace Leeto\YiiBlade\Tests;

use Closure;
use Leeto\YiiBlade\DirectiveInterface;
use Leeto\YiiBlade\DirectiveType;

final class TestStringableDirective implements DirectiveInterface
{
    public function getType(): DirectiveType
    {
        return DirectiveType::STRINGABLE;
    }

    public function getName(): string
    {
        return 'testStringable';
    }

    public function handler(): Closure
    {
        return static fn (TestStringableValue $value): string => 'stringable ' . $value->value;
    }
}
