# Annotaction
### Annotated Action Controllers

Annotaction enables you to create and use single-action controllers (Actions)
that utilize Annotations to define their corresponding Route.

```php
<?php

use Cryode\Annotaction\Annotation\Route;

/**
 * @Route("/blog/post/{postId}", middleware="web", name="blog.post.view")
 */
final class ViewBlogPost
{
    public function __invoke($postId)
    {
        // ...
    }
}
```

## Benefits

- Single-purpose controllers (Actions) help keep your code organized, slim, and can help with performance.
- Route definition in the Action file keeps concerns together - no guessing what URI a controller has
- No performance hit when using route caching

## Installation

Requirements: PHP 7.1+

Annotaction installs via [Composer](https://getcomposer.org).

    composer require cryode/annotaction
    
Laravel's package discovery should automatically load the service provider after installation.
If you have disabled auto-discovery, add the service provider to your app manually:

    \Cryode\Annotaction\AnnotactionServiceProvider::class
    
Optional for PhpStorm users, the [PHP Annotations plugin](https://plugins.jetbrains.com/plugin/7320-php-annotations)
is very useful.

## Configuration

Run the publish command to add the configuration file to your application (optional, if you'd like to change the default values):

    php artisan vendor:publish --provider="Cryode\Annotaction\AnnotactionServiceProvider" --tag="config"

### Configuration Options

- **action_dir** | `string` | Default: `/app/Http/Actions`  
  The directory where your Actions live and are generated.

- **default_middleware** | `array[string]` | Default: `['web']`  
  The default middleware Annotation value when generating an Action via Artisan.
  
- **api_middleware** | `array[string]` | Default: `['api']`  
  The default middleware Annotation value when generating an Action via Artisan with the `--api` flag.
  
- **generate_strict_types** | `bool` | Default: `true`  
  Flag to place `define(strict_types=1)` in generated Action files.

## Create Actions via Artisan

Annotaction comes with the Artisan command `make:action` to help generate Action files.

Simple usage: `$ php artisan make:action MyActionName`

Options:

    --resource                 Flag to create collection of Resource Actions
    --api                      Flag to specify Action(s) as meant for an API
    --path[=PATH]              The route URI/path for the Action [default: "/"]
    --name[=NAME]              The route Name for the Action
    --method[=METHOD]          The HTTP Method for the Action
    --middleware[=MIDDLEWARE]  The Route Middleware for the Action; default in config
    --force                    Force recreation of Action if it already exists

The `--api` flag has two effects:

1. The `api_middleware` config value will be used in the generated Annotation if no `--middleware` value is specified.
2. If creating Resource actions via `--resource`, it will skip Create and Edit, since an API has no user interface.
