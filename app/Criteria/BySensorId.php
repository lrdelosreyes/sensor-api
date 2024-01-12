<?php

namespace App\Criteria;

use Illuminate\Database\Eloquent\Builder;

class BySensorId implements Criteria
{
    private $sensorId;

    public function __construct($sensorId)
    {
        $this->sensorId = $sensorId;
    }

    public function apply(Builder $query): Builder
    {
        return $query->where('sensor_id', $this->sensorId);
    }
}
