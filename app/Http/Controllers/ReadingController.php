<?php

namespace App\Http\Controllers;

use App\Models\Reading;
use App\Http\Requests\StoreReadingRequest;
use App\Models\Sensor;

class ReadingController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreReadingRequest $request)
    {
        $created = Reading::query()->create([
            'sensor_id' => $request->sensor_id,
            'reading_value' => $request->reading_value,
            'unit' => $request->sensor_type === 'rain' ? 'mm' : 'm'
        ]);

        return response()->json([
            'data' => $created
        ], 200);
    }
}
