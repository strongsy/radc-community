<?php

namespace App\Traits\database\seeders;

use Random\RandomException;

trait HasMorphRelations
{
    /**
     * Helper method to get a collection of random models from multiple model types.
     *
     * @param  array  $modelClasses  Array of model class names
     * @param  int  $minCount  Minimum number of models to return from each type
     * @param  int  $maxCount  Maximum number of models to return from each type
     * @return array Array of models with their types
     *
     * @throws RandomException
     */
    protected function getRandomModelsForMorphRelation(array $modelClasses, int $minCount = 1, int $maxCount = 5): array
    {
        $models = [];

        foreach ($modelClasses as $modelClass) {
            $instances = $modelClass::all()->take(random_int($minCount, $maxCount));

            if ($instances->isEmpty()) {
                continue;
            }

            foreach ($instances as $instance) {
                $models[] = [
                    'id' => $instance->id,
                    'type' => $modelClass,
                    'instance' => $instance,
                ];
            }
        }

        return $models;
    }
}
