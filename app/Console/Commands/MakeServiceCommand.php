<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name} {--force} {--m|model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $storage = \Storage::disk('root');
        $name = $this->argument('name');
        $path = "app/Services/{$name}.php";

        $force = $this->option('force');
        if ($storage->exists($path) && !$force) {
            $this->error("Service {$name} already exists. Use --force to overwrite.");
            return;
        }

        $namespace = explode('/', $name);
        $serviceName = array_pop($namespace);
        $namespace = trim(implode('\\', $namespace));

        $model = $this->option('model');
        if (!$model && $this->hasOption('model')) {
            $model = str_replace('Service', '', $serviceName);
            $modelName = class_basename($model);
        } 

        if ($this->option('model')) {
            $model = $model ? str_replace('/', '\\', $model) : null;
            $modelName = $model ? class_basename($model) : null;
        }

        if (isset($modelName)) {
            $stub = file_get_contents(resource_path('stubs/service-model.stub'));
            $stub = str_replace('{{ model }}', $model, $stub);
            $stub = str_replace('{{ modelName }}', $modelName, $stub);
        } else {
            $stub = file_get_contents(resource_path('stubs/service.stub'));
        }
        $stub = str_replace('{{ class }}', $serviceName, $stub);
        $stub = str_replace('{{ namespace }}', $namespace, $stub);
        if (!empty($namespace)) {
            $stub = str_replace('{{ useBaseService }}', "\nuse App\\Services\\BaseService;", $stub);
        } else {
            $stub = str_replace('{{ useBaseService }}', '', $stub);
        }

        $storage->put($path, $stub);

        $path = $storage->path($path);

        $this->info("Service <options=bold>[{$path}]</> created successfully.");
    }
}
