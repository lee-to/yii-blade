# Blade Template Engine for Yii3

[![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen.svg)](#)

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
        'cachePath' => 'runtime/cache/blade/views',
        'componentNamespaces' => [
            'App\\View\\Components' => 'x',
        ],
        'anonymousComponentNamespaces' => [
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

## Layout

Create a layout template:

```blade
{{-- views/layouts/main.blade.php --}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'My application')</title>
</head>
<body>
    <header>
        <nav>
            <a href="/">Home</a>
        </nav>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>
```

Use it from a page template:

```blade
{{-- views/site/index.blade.php --}}
@extends('layouts.main')

@section('title', 'Home')

@section('content')
    <h1>Hello, {{ $name }}!</h1>
@endsection
```

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
