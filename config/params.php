<?php

declare(strict_types=1);

use Leeto\YiiBlade\BladeRenderer;
use Yiisoft\Definitions\Reference;

return [
    'yiisoft/view' => [
        'renderers' => [
            'blade.php' => Reference::to(BladeRenderer::class),
        ],
    ],

    'lee-to/yii-blade' => [
        'paths' => [],
        'cachePath' => 'runtime/cache/blade/views',
        'componentNamespaces' => [],
        'anonymousComponentNamespaces' => [],
        'directives' => [],
    ],
];
