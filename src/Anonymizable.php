<?php

namespace Outsidaz\LaravelDataAnonymization;

use Faker\Generator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LogicException;

trait Anonymizable
{
    public function anonymizableCondition(): Builder
    {
        return static::hasMacro('withTrashed') ? static::withTrashed() : static::query();
    }

    public function anonymizableAttributes(Generator $faker): array
    {
        $class = new (static::class)();

        if (! is_subclass_of($class, Model::class)) {
            throw new LogicException('Please implement the anonymizable trait on an Eloquent Model.');
        }

        // Check if the model's factory has an implementation of the anonymizable attributes that the anonymizer can use
        if ($class::factory()) {
            if (method_exists($class::factory(), 'anonymizableAttributes')) {
                return [];
            }
        }

        throw new LogicException(
            'Please implement the anonymizableAttributes() method on your model or the model\'s factory.'
        );
    }
}
