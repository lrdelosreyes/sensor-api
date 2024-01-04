<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Http\Requests\StoreSensorRequest;
use App\Http\Requests\UpdateSensorRequest;
use App\Models\Reading;
use Carbon\Carbon;

class SensorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sensors = Sensor::query()
            ->with('reading')
            ->get();

        return response()->json([
            'data' => $sensors
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function indexPaginated()
    {
        $sensors = Sensor::query()->paginate(15);

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
        $data = $sensor::where('id', $sensor->id)->firstOrFail();

        $yearly = Reading::query()
            ->select('reading_value', 'unit', 'logged_at')
            ->where('sensor_id', $sensor->id)
            ->whereBetween('logged_at', [
                Carbon::createFromDate(Carbon::now()->year, '01', '01'),
                Carbon::now()
            ])
            ->orderBy('logged_at')
            ->get();
        $monthly = Reading::query()
            ->select('reading_value', 'unit', 'logged_at')
            ->where('sensor_id', $sensor->id)
            ->whereBetween('logged_at', [
                Carbon::createFromDate(Carbon::now()->year, Carbon::now()->month, '01'),
                Carbon::now()
            ])
            ->orderBy('logged_at', 'asc')
            ->get();
        $daily = Reading::query()
            ->select('reading_value', 'unit', 'logged_at')
            ->where('sensor_id', $sensor->id)
            ->whereBetween('logged_at', [Carbon::now(), Carbon::now()])
            ->orderBy('logged_at', 'asc')
            ->get();

        $data->yearly_readings = $yearly;
        $data->monthly_readings = $monthly;
        $data->daily_readings = $daily;

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