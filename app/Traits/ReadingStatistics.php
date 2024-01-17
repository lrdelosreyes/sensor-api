<?php

namespace App\Traits;

trait ReadingStatistics
{
    public function calculateStatistics($period, string $format, string $function = 'max')
    {
        return $period->map(function ($row) use ($format, $function) {
            return (object) [
                'logged_at' => $row[0]['logged_at']->format($format),
                'reading_value' => $row->{$function}('reading_value'),
                'unit' => $row[0]['unit'],
            ];
        });
    }
}
