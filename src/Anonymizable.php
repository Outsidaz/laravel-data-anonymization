<?php

namespace Outsidaz\LaravelDataAnonymization;;

use Faker\Generator;
use Illuminate\Database\Eloquent\Builder;
use LogicException;

trait Anonymizable
{
    public function anonymizableCondition(): Builder
    {
        return static::hasMacro('withTrashed') ? static::withTrashed() : static::query();
    }

    public function anonymizableAttributes(Generator $faker): array
    {
        throw new LogicException('Please implement the anonymizable method on your model.');
    }
}