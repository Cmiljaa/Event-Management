<?php

namespace App\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

trait CanLoadRelationships
{
  public function loadRelationships(Model|QueryBuilder|EloquentBuilder|HasMany $for, Request $request, ?array $relations = null
  ):Model|QueryBuilder|EloquentBuilder|HasMany {
    $relations = $relations ?? $this->relations ?? [];

    foreach ($relations as $relation) {
      $for->when(
        $this->shouldIncludeRelation($relation, $request),
        fn($q) => $for instanceof Model ? $for->load($relation) : $q->with($relation)
      );
    }

    return $for;
  }

  protected function shouldIncludeRelation(string $relation, Request $request): bool
  {
    $include = $request->query('include');

    if (!$include) {
      return false;
    }

    $relations = array_map('trim', explode(',', $include));

    return in_array($relation, $relations);
  }
}