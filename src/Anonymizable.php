<?php

namespace App\Packages\Anonymizer\src;

use Faker\Generator;
use Illuminate\Database\Eloquent\Builder;
use LogicException;

trait Anonymizable
{
    public function anonymizableCondition(): Builder
    {
        return static::withTrashed();
    }
    public function anonymizableAttributes(Generator $faker): array
    {
        throw new LogicException('Please implement the anonymizable method on your model.');
    }
}