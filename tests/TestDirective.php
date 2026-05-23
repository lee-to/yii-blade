<?php

declare(strict_types=1);

namespace Leeto\YiiBlade\Tests;

use Closure;
use Leeto\YiiBlade\DirectiveInterface;
use Leeto\YiiBlade\DirectiveType;

final class TestDirective implements DirectiveInterface
{
    public function getType(): DirectiveType
    {
        return DirectiveType::DEFAULT;
    }

    public function getName(): string
    {
        return 'testDirective';
    }

    public function handler(): Closure
    {
        return static fn (?string $expression): string => "<?php echo 'TEST: ' . $expression; ?>";
    }
}
