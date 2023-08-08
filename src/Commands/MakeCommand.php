<?php

namespace pxlrbt\LaravelDatabaseState\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCommand extends Command
{
    protected $signature = 'make:db-state {name} {--force}';

    protected $description = 'Create a new Database state class';

    public function handle()
    {
        $namespace = 'Database\\States';
        $name = $this->argument('name');
        $class = Str::studly($name);

        if (! $this->isClassNameValid($class)) {
            $this->line("<options=bold,reverse;fg=red> WHOOPS! </> 😳 \n");
            $this->line("<fg=red;options=bold>Class is invalid:</> {$class}");

            return;
        }

        if ($this->isReservedClassName($class)) {
            $this->line("<options=bold,reverse;fg=red> WHOOPS! </> 😳 \n");
            $this->line("<fg=red;options=bold>Class is reserved:</> {$class}");

            return;
        }

        $force = $this->option('force');

        $data = [
            'namespace' => $namespace,
            'class' => $class,
        ];

        $class = $this->createClass($class, $data, $force);

        if ($class) {
            $this->line("<options=bold,reverse;fg=green> Database state created: </> $class");
        }
    }

    protected function createClass($name, $data, $force = false)
    {
        $path = database_path("States/{$name}.php");

        if (File::exists($path) && ! $force) {
            $this->line("<fg=red;options=bold,reverse> Class already exists: </> {$path}");

            return false;
        }

        $this->copyStubToApp('State', $path, $data);

        return $path;
    }

    public function isClassNameValid($name)
    {
        return preg_match("/^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$/", $name);
    }

    public function isReservedClassName($name)
    {
        return array_search(strtolower($name), $this->getReservedName()) !== false;
    }

    protected function copyStubToApp(string $stub, string $targetPath, array $replacements = []): void
    {
        $filesystem = app(Filesystem::class);

        if (! File::exists($stubPath = base_path("stubs/laravel-db-state/{$stub}.stub"))) {
            $stubPath = realpath(__DIR__."/../../stubs/{$stub}.stub");
        }

        $stub = Str::of($filesystem->get($stubPath));

        foreach ($replacements as $key => $replacement) {
            $stub = $stub->replace("{{ {$key} }}", $replacement);
        }

        $stub = (string) $stub;

        File::makeDirectory(dirname($targetPath), force: true);

        File::put($targetPath, $stub);
    }

    private function getReservedName()
    {
        return [
            'parent',
            'component',
            'interface',
            '__halt_compiler',
            'abstract',
            'and',
            'array',
            'as',
            'break',
            'callable',
            'case',
            'catch',
            'class',
            'clone',
            'const',
            'continue',
            'declare',
            'default',
            'die',
            'do',
            'echo',
            'else',
            'elseif',
            'empty',
            'enddeclare',
            'endfor',
            'endforeach',
            'endif',
            'endswitch',
            'endwhile',
            'eval',
            'exit',
            'extends',
            'final',
            'finally',
            'fn',
            'for',
            'foreach',
            'function',
            'global',
            'goto',
            'if',
            'implements',
            'include',
            'include_once',
            'instanceof',
            'insteadof',
            'interface',
            'isset',
            'list',
            'namespace',
            'new',
            'or',
            'print',
            'private',
            'protected',
            'public',
            'require',
            'require_once',
            'return',
            'static',
            'switch',
            'throw',
            'trait',
            'try',
            'unset',
            'use',
            'var',
            'while',
            'xor',
            'yield',
        ];
    }
}
