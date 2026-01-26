<?php

declare(strict_types=1);

namespace Leeto\YiiBlade;

enum DirectiveType: string
{
    case DEFAULT = 'directive';

    case IF = 'if';

    case STRINGABLE = 'stringable';
}
