<?php

declare(strict_types=1);

namespace Leeto\YiiBlade\Tests;

use Leeto\YiiBlade\Config\BladeConfig;
use PHPUnit\Framework\TestCase;

final class BladeConfigTest extends TestCase
{
    public function testStoresConfigurationValues(): void
    {
        $config = new BladeConfig(
            paths: ['default' => '/views'],
            cachePath: '/cache/blade',
            componentNamespaces: ['App\\View\\Components' => 'x'],
            anonymousComponentNamespaces: ['/views/components' => 'x'],
            directives: [TestDirective::class],
        );

        self::assertSame(['default' => '/views'], $config->getPaths());
        self::assertSame('/cache/blade', $config->getCacheDir());
        self::assertSame('/cache/blade', $config->getCachePath());
        self::assertSame(['App\\View\\Components' => 'x'], $config->getComponentNamespaces());
        self::assertSame(['/views/components' => 'x'], $config->getAnonymousComponentNamespaces());
        self::assertSame([TestDirective::class], $config->getDirectives());
    }

    public function testKeepsLegacyCacheDirNamedArgument(): void
    {
        $config = new BladeConfig(cacheDir: '/legacy-cache');

        self::assertSame('/legacy-cache', $config->getCacheDir());
        self::assertSame('/legacy-cache', $config->getCachePath());
    }
}
