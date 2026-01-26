# Blade Template Engine for Yii3

This package provides [Laravel Blade](https://laravel.com/docs/blade) template engine support for the **Yii3 framework**.

## Requirements

- PHP **8.2+**
- Yii3
- Composer 2.5+

## Installation

```shell
composer require lee-to/yii-blade
```

Or install via local `path` repository:

```json
"repositories": [
    {
        "type": "path",
        "url": "packages/yii-blade",
        "options": {
            "versions": {
                "lee-to/yii-blade": "1.x-dev"
            },
            "symlink": true
        }
    }
]
```

## Configuration

Configure the package in your `config/params.php`:

```php
return [
    'lee-to/yii-blade' => [
        'paths' => [
            'default' => '@views',
            'admin' => '@views/admin',
        ],
        'cache_dir' => 'runtime/cache/blade/views',
        'component_namespaces' => [
            'App\\View\\Components' => 'x',
        ],
        'anonymous_component_namespaces' => [
            '@views/components' => 'x',
        ],
        'directives' => [
            // App\Blade\Directives\MyDirective::class,
        ],
    ],
];
```

## Usage

Create a Blade template in your views directory:

```blade
{{-- views/hello.blade.php --}}
<h1>Hello, {{ $name }}!</h1>

@if($showGreeting)
    <p>Welcome to Yii3 with Blade!</p>
@endif
```

The package automatically registers as a renderer for `.blade.php` files.

## Custom Directives

Create a custom directive by implementing `DirectiveInterface`:

```php
<?php

namespace App\Blade\Directives;

use Closure;
use Leeto\YiiBlade\DirectiveInterface;
use Leeto\YiiBlade\DirectiveType;

final class DateTimeDirective implements DirectiveInterface
{
    public function getType(): DirectiveType
    {
        return DirectiveType::DEFAULT;
    }

    public function getName(): string
    {
        return 'datetime';
    }

    public function handler(): Closure
    {
        return fn(?string $expression): string => "<?php echo date('Y-m-d H:i:s', $expression); ?>";
    }
}
```

Register it in params:

```php
'directives' => [
    App\Blade\Directives\DateTimeDirective::class,
],
```

Use in templates:

```blade
@datetime(time())
```

## Directive Types

- `DirectiveType::DEFAULT` - Standard directive (`@name($expression)`)
- `DirectiveType::IF` - Conditional directive (`@name($condition) ... @endname`)
- `DirectiveType::STRINGABLE` - Stringable handler for custom object rendering

## Features

- Full Blade syntax support
- Components and slots
- Custom directives
- Anonymous components
- Template inheritance (`@extends`, `@section`, `@yield`)
- Includes (`@include`, `@each`)
- Conditionals (`@if`, `@unless`, `@isset`, `@empty`)
- Loops (`@foreach`, `@for`, `@while`, `@forelse`)
- And more...

## License

MIT
