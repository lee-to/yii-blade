<?php

declare(strict_types=1);

namespace Leeto\YiiBlade\Tests;

use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Leeto\YiiBlade\BladeRenderer;
use Leeto\YiiBlade\Config\BladeConfig;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Yiisoft\View\ViewInterface;

final class BladeRendererTest extends TestCase
{
    private string $root;

    protected function setUp(): void
    {
        $this->root = sys_get_temp_dir() . '/yii-blade-' . bin2hex(random_bytes(6));

        mkdir($this->root . '/views', recursive: true);
        mkdir($this->root . '/cache', recursive: true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->root);
    }

    public function testRendersBladeTemplate(): void
    {
        file_put_contents($this->root . '/views/hello.blade.php', 'Hello, {{ $name }}!');

        $renderer = new BladeRenderer(new BladeConfig(
            paths: [$this->root . '/views'],
            cachePath: $this->root . '/cache',
        ));

        self::assertSame(
            'Hello, Ada!',
            $renderer->renderTemplate('hello.blade.php', ['name' => 'Ada'])->render(),
        );
    }

    public function testRendersTemplateThroughViewRendererInterface(): void
    {
        file_put_contents($this->root . '/views/interface.blade.php', 'Rendered {{ $name }}');

        $renderer = new BladeRenderer(new BladeConfig(
            paths: [$this->root . '/views'],
            cachePath: $this->root . '/cache',
        ));

        self::assertSame(
            'Rendered via interface',
            $renderer->render(
                $this->createStub(ViewInterface::class),
                'interface.blade.php',
                ['name' => 'via interface'],
            ),
        );
    }

    public function testReusesFactoryUntilReset(): void
    {
        file_put_contents($this->root . '/views/cached.blade.php', 'Cached {{ $name }}');

        $renderer = new BladeRenderer(new BladeConfig(
            paths: [$this->root . '/views'],
            cachePath: $this->root . '/cache',
        ));

        self::assertSame('Cached first', $renderer->renderTemplate('cached.blade.php', ['name' => 'first'])->render());
        self::assertSame('Cached second', $renderer->renderTemplate('cached.blade.php', ['name' => 'second'])->render());

        $renderer->reset();
        self::assertSame('Cached third', $renderer->renderTemplate('cached.blade.php', ['name' => 'third'])->render());
    }

    public function testResetWithoutInitializedFactoryClearsContainer(): void
    {
        $renderer = new BladeRenderer(new BladeConfig());

        $renderer->reset();

        self::assertTrue(true);
    }

    public function testRendersCustomDirective(): void
    {
        file_put_contents($this->root . '/views/directive.blade.php', '@testDirective("content")');

        $renderer = new BladeRenderer(new BladeConfig(
            paths: [$this->root . '/views'],
            cachePath: $this->root . '/cache',
            directives: [TestDirective::class],
        ));

        self::assertSame(
            'TEST: content',
            $renderer->renderTemplate('directive.blade.php', [])->render(),
        );
    }

    public function testRendersRuntimeClosureDirective(): void
    {
        file_put_contents($this->root . '/views/closure.blade.php', '@runtimeDirective("content")');

        $renderer = (new BladeRenderer(new BladeConfig(
            paths: [$this->root . '/views'],
            cachePath: $this->root . '/cache',
        )))->directives([
            'runtimeDirective' => static fn (?string $expression): string => "<?php echo strtoupper($expression); ?>",
        ]);

        self::assertSame(
            'CONTENT',
            $renderer->renderTemplate('closure.blade.php', [])->render(),
        );
    }

    public function testRegistersConditionalDirective(): void
    {
        $renderer = new BladeRenderer(new BladeConfig(
            paths: [$this->root . '/views'],
            cachePath: $this->root . '/cache',
        ));
        $compiler = new BladeCompiler(new Filesystem(), $this->root . '/cache');
        $method = new ReflectionMethod(BladeRenderer::class, 'addDirectives');

        self::assertSame($compiler, $method->invoke($renderer, $compiler, TestIfDirective::class));
    }

    public function testRegistersStringableDirective(): void
    {
        $renderer = new BladeRenderer(new BladeConfig(
            paths: [$this->root . '/views'],
            cachePath: $this->root . '/cache',
        ));
        $compiler = new BladeCompiler(new Filesystem(), $this->root . '/cache');
        $method = new ReflectionMethod(BladeRenderer::class, 'addDirectives');

        self::assertSame($compiler, $method->invoke($renderer, $compiler, TestStringableDirective::class));
    }

    public function testMergesRuntimeConfiguration(): void
    {
        mkdir($this->root . '/extra-views');
        mkdir($this->root . '/components');
        file_put_contents($this->root . '/extra-views/merged.blade.php', 'Merged {{ $name }}');

        $renderer = (new BladeRenderer(new BladeConfig(
            paths: ['default' => $this->root . '/views'],
            cachePath: $this->root . '/cache',
            componentNamespaces: ['App\\View\\Components' => 'x'],
            anonymousComponentNamespaces: [$this->root . '/components' => 'ui'],
        )))
            ->paths(['extra' => $this->root . '/extra-views'])
            ->components(['Runtime\\View\\Components' => 'runtime'])
            ->anonymousComponents([$this->root . '/components' => 'runtime-ui']);

        self::assertSame(
            'Merged config',
            $renderer->renderTemplate('merged.blade.php', ['name' => 'config'])->render(),
        );

        self::assertSame('App', Container::getInstance()->make(Application::class)->getNamespace());
    }

    private function removeDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $files = array_diff(scandir($directory) ?: [], ['.', '..']);

        foreach ($files as $file) {
            $path = $directory . '/' . $file;

            if (is_dir($path)) {
                $this->removeDirectory($path);

                continue;
            }

            unlink($path);
        }

        rmdir($directory);
    }
}
