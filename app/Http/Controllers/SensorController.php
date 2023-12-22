<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Http\Requests\StoreSensorRequest;
use App\Http\Requests\UpdateSensorRequest;

class SensorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sensors = Sensor::query()
            ->with('readings')
            ->get();

        return response()->json([
            'data' => $sensors
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSensorRequest $request)
    {
        $created = Sensor::query()->create([
            'name' => $request->name,
            'type' => $request->type,
            'lat' => $request->lat,
            'long' => $request->long,
        ]);

        return response()->json([
            'data' => $created
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Sensor $sensor)
    {
        $data = $sensor::with('readings')
            ->where('id', $sensor->id)
            ->firstOrFail();

        return response()->json([
            'data' => $data
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSensorRequest $request, Sensor $sensor)
    {
        $updated = $sensor->update([
            'name' => $request->name ?? $sensor->name,
            'type' => $request->type ?? $sensor->type,
            'lat' => $request->lat ?? $sensor->lat,
            'long' => $request->long ?? $sensor->long,
        ]);

        if (!$updated) {
            return response()->json([
                'errors' => [
                    'Failed to update.'
                ]
            ], 400);
        }

        return response()->json([
            'data' => $sensor
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sensor $sensor)
    {
        $deleted = $sensor->forceDelete();

        if (!$deleted) {
            return response()->json([
                'errors' => [
                    'Could not delete the sensor.'
                ]
            ], 400);
        }

        return response()->json([
            'data' => 'success'
        ], 200);
    }
}
