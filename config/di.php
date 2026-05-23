<?php

declare(strict_types=1);

use Leeto\YiiBlade\BladeRenderer;
use Leeto\YiiBlade\Config\BladeConfig;

/** @var array $params */

return [
    BladeConfig::class => [
        'class' => BladeConfig::class,
        '__construct()' => [
            'paths' => $params['lee-to/yii-blade']['paths'],
            'cachePath' => $params['lee-to/yii-blade']['cachePath']
                ?? $params['lee-to/yii-blade']['cache_dir'],
            'componentNamespaces' => $params['lee-to/yii-blade']['componentNamespaces']
                ?? $params['lee-to/yii-blade']['component_namespaces'],
            'anonymousComponentNamespaces' => $params['lee-to/yii-blade']['anonymousComponentNamespaces']
                ?? $params['lee-to/yii-blade']['anonymous_component_namespaces'],
            'directives' => $params['lee-to/yii-blade']['directives'],
        ],
    ],
    BladeRenderer::class => [
        'class' => BladeRenderer::class,
        'reset' => function (): void {
            $this->reset();
        },
    ],
];
