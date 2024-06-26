<?php

namespace Outsidaz\LaravelDataAnonymization;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class Anonymizer
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create(
            config('anonymizer.locale', Factory::DEFAULT_LOCALE)
        );
    }

    public function isBlockedEnvironment(): bool
    {
        return in_array(config('app.env'), config('anonymizer.blocked_env', []));
    }

    private function getAllModels(): array
    {
        return File::allFiles(config('anonymizer.models_path', app_path('Models')));
    }

    private function getAllClasses(): array
    {
        return collect($this->getAllModels())
                ->map(function(SplFileInfo $file){
                    $path = $file->getRelativePathName();
                    $class = str_replace('.php', '', $path);
                    return config('anonymizer.models_namespace', '\\App\\Models') . '\\' . str_replace('/', '\\', $class);
                })
                ->toArray();
    }

    public function getAnonymizableClasses(): array
    {
        return array_filter(
            $this->getAllClasses(),
            fn($class) => in_array(Anonymizable::class, class_uses($class), true)
        );
    }

    private function getQuery(Model $model): Builder
    {
        return $model->anonymizableCondition();
    }

    public function getCount(Model $model): int
    {
        return $this->getQuery($model)->count();
    }

    public function getChunk(Model $model, callable $call): bool
    {
        return $this->getQuery($model)->chunk(
            config('anonymizer.chunk_size', 1000),
            $call
        );
    }

    public function changeData(Model $model): bool
    {
        if ($this->isBlockedEnvironment() && config('anonymizer.force_blocked_env', true)) {
            throw new \Exception(sprintf("Environment '%s' has blocking enforced.", config('app.env')));
        }

        return $model
            ->setTouchedRelations([]) // disable touch owners
            ->updateQuietly( // disable events handling
                $model->anonymizableAttributes($this->faker)
            );
    }
}