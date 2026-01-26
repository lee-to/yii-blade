<?php

declare(strict_types=1);

namespace Leeto\YiiBlade;

use Closure;

interface DirectiveInterface
{
    public function getType(): DirectiveType;

    public function getName(): string;

    public function handler(): Closure;
}
