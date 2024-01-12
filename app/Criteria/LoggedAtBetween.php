<?php

namespace App\Criteria;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class LoggedAtBetween implements Criteria
{
    private $startDate;
    private $endDate;

    public function __construct(Carbon $startDate, Carbon $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function apply(Builder $query): Builder
    {
        return $query->whereBetween('logged_at', [$this->startDate, $this->endDate]);
    }
}
