<?php

declare(strict_types=1);

namespace Cryode\Annotaction\Console;

use function Cryode\Annotaction\Util\comma_str_to_array;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Arr;

class ActionMake extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:action {action : Name of the Action class to create}
                                        {--resource : Flag to create collection of Resource Actions}
                                        {--api : Flag to specify Action(s) as meant for an API}
                                        {--path=/ : The route URI/path for the Action}
                                        {--name= : The route Name for the Action}
                                        {--method= : The HTTP Method for the Action}
                                        {--middleware= : The Route Middleware for the Action; default in config}
                                        {--force : Force recreation of Action if it already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Action class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Action';

    /**
     * {@inheritdoc}
     */
    public function handle(): bool
    {
        if ($this->option('resource')) {
            $this->makeResourceActions();

            return true;
        }

        $name = $this->qualifyClass($this->getNameInput());

        $path = $this->getPath($name);

        // First we will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if (( ! $this->hasOption('force') ||
                ! $this->option('force')) &&
            $this->alreadyExists($this->getNameInput())) {
            $this->error($name.' '.$this->type.' already exists!');

            return false;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($name));

        $this->info($name.' '.$this->type.' created successfully.');

        return true;
    }

    protected function makeResourceActions(): void
    {
        $nameInput = $this->getNameInput();

        $this->info('Creating resource actions for '.$nameInput);

        $class = $this->getClass();
        $slug = \str_slug($this->getClass());

        foreach ($this->getResourceActions() as $action => [$path, $method]) {
            $this->call('make:action', [
                'action' => $nameInput.'/'.$class.\ucfirst($action),
                '--api' => $this->hasApiFlag(),
                '--path' => $slug.$path,
                '--name' => $slug.'.'.$action,
                '--method' => $method,
            ]);
        }
    }

    protected function getResourceActions(): array
    {
        $actions = [
            'index' => ['', 'GET'],
            'create' => ['/create', 'GET'],
            'store' => ['', 'POST'],
            'show' => ['/{id}', 'GET'],
            'edit' => ['/{id}/edit', 'GET'],
            'update' => ['/{id}', 'PUT'],
            'destroy' => ['/{id}', 'DELETE'],
        ];

        if ($this->hasApiFlag()) {
            return Arr::only($actions, ['index', 'store', 'show', 'update', 'destroy']);
        }

        return $actions;
    }

    /**
     * {@inheritdoc}
     */
    protected function getNameInput(): string
    {
        return \trim($this->argument('action'));
    }

    /**
     * {@inheritdoc}
     */
    protected function getStub(): string
    {
        return __DIR__.'/stubs/action.stub';
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\\Http\\Actions';
    }

    /**
     * Get the class name from the fully-qualified namespace.
     *
     * @return string
     */
    protected function getClass(): string
    {
        $name = $this->qualifyClass($this->getNameInput());
        $namespace = $this->getNamespace($name);

        return \str_replace($namespace.'\\', '', $name);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildClass($name): string
    {
        $stub = parent::buildClass($name);

        return $this->replaceStrictTypes($stub)
            ->replaceRoute($stub);
    }

    /**
     * If strict types are enabled, add definition to the file.
     *
     * @param string $stub
     *
     * @return self
     */
    protected function replaceStrictTypes(string &$stub): self
    {
        $replace = (bool) \config('annotaction.generate_strict_types') ? "declare(strict_types=1);\n" : '';

        $stub = \str_replace("declare();\n", $replace, $stub);

        return $this;
    }

    /**
     * Add in the Route annotation parameters based on input options.
     *
     * @param string $stub
     *
     * @return string
     */
    protected function replaceRoute(string $stub): string
    {
        $routeParams = [
            \sprintf('"%s"', $this->getRoutePath()),
        ];

        if ( ! empty($methods = $this->getRouteMethods())) {
            $routeParams[] = \sprintf('method="%s"', \implode(', ', $methods));
        }

        if ( ! empty($middleware = $this->getRouteMiddleware())) {
            $routeParams[] = \sprintf('middleware="%s"', \implode(',', $middleware));
        }

        if (null !== ($name = $this->getRouteName())) {
            $routeParams[] = \sprintf('name="%s"', $name);
        }

        return \str_replace('DummyRoute', \implode(', ', $routeParams), $stub);
    }

    protected function getRoutePath(): string
    {
        return $this->option('path');
    }

    protected function getRouteMethods(): array
    {
        $input = comma_str_to_array(\strtolower((string) $this->option('method')));

        // An annotated Action will default to GET if no method is specified.
        // If GET is the only method, we can safely leave it out of the annotation.
        if (['get'] === $input) {
            return [];
        }

        return $input;
    }

    protected function getRouteMiddleware(): array
    {
        $input = $this->option('middleware');

        if (null !== $input) {
            return comma_str_to_array($input);
        }

        if ($this->hasApiFlag()) {
            return \config('annotaction.api_middleware');
        }

        return \config('annotaction.default_middleware');
    }

    protected function getRouteName(): ?string
    {
        return $this->option('name');
    }

    protected function hasApiFlag(): bool
    {
        return (bool) $this->option('api');
    }
}
