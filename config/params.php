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
        'cache_dir' => 'runtime/cache/blade/views',
        'component_namespaces' => [],
        'anonymous_component_namespaces' => [],
        'directives' => [],
    ],
];
