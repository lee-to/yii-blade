<?php

declare(strict_types=1);

namespace Leeto\YiiBlade\Tests;

use Leeto\YiiBlade\Config\BladeConfig;
use PHPUnit\Framework\TestCase;

final class ConfigFilesTest extends TestCase
{
    public function testParamsUseYiiNamingConvention(): void
    {
        $params = require dirname(__DIR__) . '/config/params.php';
        $packageParams = $params['lee-to/yii-blade'];

        self::assertArrayHasKey('cachePath', $packageParams);
        self::assertArrayHasKey('componentNamespaces', $packageParams);
        self::assertArrayHasKey('anonymousComponentNamespaces', $packageParams);
        self::assertArrayNotHasKey('cache_dir', $packageParams);
        self::assertArrayNotHasKey('component_namespaces', $packageParams);
        self::assertArrayNotHasKey('anonymous_component_namespaces', $packageParams);
    }

    public function testDiMapsCamelCaseParams(): void
    {
        $params = [
            'lee-to/yii-blade' => [
                'paths' => ['default' => '/views'],
                'cachePath' => '/cache',
                'componentNamespaces' => ['App\\View\\Components' => 'x'],
                'anonymousComponentNamespaces' => ['/views/components' => 'x'],
                'directives' => [TestDirective::class],
            ],
        ];

        $di = (static function () use ($params): array {
            return require dirname(__DIR__) . '/config/di.php';
        })();

        self::assertSame(
            [
                'paths' => ['default' => '/views'],
                'cachePath' => '/cache',
                'componentNamespaces' => ['App\\View\\Components' => 'x'],
                'anonymousComponentNamespaces' => ['/views/components' => 'x'],
                'directives' => [TestDirective::class],
            ],
            $di[BladeConfig::class]['__construct()'],
        );
    }

    public function testDiKeepsLegacySnakeCaseParamsFallback(): void
    {
        $params = [
            'lee-to/yii-blade' => [
                'paths' => ['default' => '/views'],
                'cache_dir' => '/cache',
                'component_namespaces' => ['App\\View\\Components' => 'x'],
                'anonymous_component_namespaces' => ['/views/components' => 'x'],
                'directives' => [TestDirective::class],
            ],
        ];

        $di = (static function () use ($params): array {
            return require dirname(__DIR__) . '/config/di.php';
        })();

        self::assertSame('/cache', $di[BladeConfig::class]['__construct()']['cachePath']);
        self::assertSame(
            ['App\\View\\Components' => 'x'],
            $di[BladeConfig::class]['__construct()']['componentNamespaces'],
        );
        self::assertSame(
            ['/views/components' => 'x'],
            $di[BladeConfig::class]['__construct()']['anonymousComponentNamespaces'],
        );
    }
}
