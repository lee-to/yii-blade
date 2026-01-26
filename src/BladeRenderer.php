<?php

declare(strict_types=1);

namespace Leeto\YiiBlade;

use Closure;
use Leeto\YiiBlade\Config\BladeConfig;
use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use ReflectionException;
use Yiisoft\View\TemplateRendererInterface;
use Yiisoft\View\ViewInterface;

final class BladeRenderer implements TemplateRendererInterface
{
    /**
     * @var string[]
     */
    private array $paths = [];

    /**
     * @var array<string, string>
     */
    private array $components = [];

    /**
     * @var array<string, string|Closure>
     */
    private array $directives = [];

    /**
     * @var array<string, string>
     */
    private array $anonymousComponents = [];

    public function __construct(
        private readonly BladeConfig $config,
    ) {}

    /**
     * @param string[] $paths
     */
    public function paths(array $paths): self
    {
        $this->paths = $paths;

        return $this;
    }

    /**
     * @param array<string, string> $components
     */
    public function components(array $components): self
    {
        $this->components = $components;

        return $this;
    }

    /**
     * @param array<string, string> $components
     */
    public function anonymousComponents(array $components): self
    {
        $this->anonymousComponents = $components;

        return $this;
    }

    /**
     * @param array<string, string|Closure> $directives
     */
    public function directives(array $directives): self
    {
        $this->directives = $directives;

        return $this;
    }

    /**
     * @return string[]
     */
    private function getPaths(): array
    {
        return array_merge(
            $this->config->getPaths(),
            $this->paths,
        );
    }

    private function getCacheDir(): string
    {
        return $this->config->getCacheDir();
    }

    /**
     * @return array<string, string>
     */
    private function getComponentNamespaces(): array
    {
        return array_merge(
            $this->config->getComponentNamespaces(),
            $this->components,
        );
    }

    /**
     * @return array<string, string>
     */
    private function getAnonymousComponentNamespaces(): array
    {
        return array_merge(
            $this->config->getAnonymousComponentNamespaces(),
            $this->anonymousComponents,
        );
    }

    /**
     * @return array<string, string|Closure>
     */
    private function getDirectives(): array
    {
        return array_merge(
            $this->config->getDirectives(),
            $this->directives,
        );
    }

    /**
     * @param  array<string, mixed>  $parameters
     *
     * @throws ReflectionException
     */
    public function renderTemplate(string $template, array $parameters): View
    {
        $paths = $this->getPaths();

        $cachePath = $this->getCacheDir();

        $compiler = new BladeCompiler(
            files: new Filesystem(),
            cachePath: $cachePath,
        );


        foreach ($this->getComponentNamespaces() as $namespace => $prefix) {
            $compiler->componentNamespace($namespace, $prefix);
        }

        foreach ($this->getAnonymousComponentNamespaces() as $directory => $prefix) {
            $compiler->anonymousComponentNamespace($directory, $prefix);
        }

        foreach ($this->getDirectives() as $name => $class) {
            $this->addDirectives($compiler, $class, $name);
        }

        $blade = new CompilerEngine(
            compiler: $compiler,
            files: new Filesystem(),
        );

        $engines = new EngineResolver();
        $engines->register('blade', static fn(): CompilerEngine => $blade);

        $factory = new Factory(
            engines: $engines,
            finder: new FileViewFinder(
                files: new Filesystem(),
                paths: $paths,
                extensions: ['blade.php'],
            ),
            events: new Dispatcher(),
        );

        $factory = $factory->addNamespace(
            '__components',
            $cachePath . '/compiled',
        );

        foreach ($paths as $namespace => $path) {
            $factory = $factory->addNamespace($namespace, $path);
        }

        foreach ($paths as $_ => $path) {
            foreach ($this->getComponentNamespaces() as $__ => $prefix) {
                $factory = $factory->addNamespace($prefix, $path);
            }

            foreach ($this->getAnonymousComponentNamespaces() as $__ => $prefix) {
                $factory = $factory->addNamespace($prefix, $path);
            }
        }

        Container::getInstance()->bind(ViewFactory::class, static fn(): Factory => $factory);
        Container::getInstance()->bind(View::class, static fn(): Factory => $factory);
        Container::getInstance()->bind('view', View::class);
        Container::getInstance()->bind(Application::class, static fn() => new class {
            public function getNamespace(): string
            {
                return 'App';
            }
        });

        $destination = str_replace('.blade.php', '', basename($template));

        return $factory->make($destination, $parameters);
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @throws ReflectionException
     */
    public function render(ViewInterface $view, string $template, array $parameters): string
    {
        return $this->renderTemplate($template, $parameters)->render();
    }

    /**
     * @param  BladeCompiler  $compiler
     * @param  class-string<DirectiveInterface>|Closure  $class
     * @param  null|int|string $name = null
     * @return BladeCompiler
     */
    protected function addDirectives(BladeCompiler $compiler, string|Closure $class, null|int|string $name = null): BladeCompiler
    {
        if ($class instanceof Closure) {
            $compiler->directive($name, $class);

            return $compiler;
        }

        $directive = new $class();

        match ($directive->getType()) {
            DirectiveType::DEFAULT => $compiler
                ->directive($directive->getName(), $directive->handler()),
            DirectiveType::IF => $compiler
                ->if($directive->getName(), $directive->handler()),
            DirectiveType::STRINGABLE => $compiler
                ->stringable($directive->handler()),
        };

        return $compiler;
    }
}
