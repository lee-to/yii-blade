<?php

declare(strict_types=1);

namespace Leeto\YiiBlade\Config;

use Leeto\YiiBlade\DirectiveInterface;

final readonly class BladeConfig
{
    public function __construct(
        private array $paths = [],
        private string $cacheDir = 'runtime/cache/blade/views',
        private array $componentNamespaces = [],
        private array $anonymousComponentNamespaces = [],
        private array $directives = [],
    ) {}

    /**
     * @return array<string, string>
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    public function getCacheDir(): string
    {
        return $this->cacheDir;
    }

    /**
     * @return array<string, string>
     */
    public function getComponentNamespaces(): array
    {
        return $this->componentNamespaces;
    }

    /**
     * @return array<string, string>
     */
    public function getAnonymousComponentNamespaces(): array
    {
        return $this->anonymousComponentNamespaces;
    }

    /**
     * @return array<class-string<DirectiveInterface>>
     */
    public function getDirectives(): array
    {
        return $this->directives;
    }
}
