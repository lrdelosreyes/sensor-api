<?php

namespace App\Http\Controllers;

// use App\Criteria\BySensorId;
// use App\Criteria\LoggedAtBetween;
use App\Models\Sensor;
use App\Http\Requests\StoreSensorRequest;
use App\Http\Requests\UpdateSensorRequest;
use App\Models\Reading;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Traits\ReadingStatistics;
// use Illuminate\Support\Facades\DB;

class SensorController extends Controller
{
    use ReadingStatistics;

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
        if (!$this->checkIfAllowed()) {
            return response()->json([
                'errors' => 'Not allowed'
            ], 402);
        }

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
        if (!$this->checkIfAllowed()) {
            return response()->json([
                'errors' => 'Not allowed'
            ], 402);
        }

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
        $sensor = Sensor::findOrFail($sensor->id); // Assuming $sensorId is available

        $data = $sensor->load('readings'); // Eager load readings for efficiency

        # TODO: Criteria for query filters
        // $readingsQuery = Reading::query()
        //     ->select('reading_value', 'unit', 'logged_at')
        //     ->withCriteria([
        //         new BySensorId($sensor->id),
        //         new LoggedAtBetween(
        //             Carbon::createFromDate(Carbon::now()->year, '01', '01'),
        //             Carbon::now()
        //         )
        //     ]);

        $_yearly = Reading::query()
            ->select('reading_value', 'unit', 'logged_at')
            ->where('sensor_id', $sensor->id)
            ->whereBetween('logged_at', [
                Carbon::createFromDate(Carbon::now()->year, '01', '01'),
                Carbon::now()
            ])
            ->orderBy('logged_at')
            ->get()
            ->groupBy(function($query) {
                return $query->logged_at->format('Y-m');
            });

        $_monthly = Reading::query()
            ->select('reading_value', 'unit', 'logged_at')
            ->where('sensor_id', $sensor->id)
            ->whereBetween('logged_at', [
                Carbon::createFromDate(Carbon::now()->year, Carbon::now()->month, '01'),
                Carbon::now()
            ])
            ->orderBy('logged_at', 'asc')
            ->get()
            ->groupBy(function($query) {
                return $query->logged_at->format('Y-m-d');
            });
        $_daily = Reading::query()
            ->select('reading_value', 'unit', 'logged_at')
            ->where('sensor_id', $sensor->id)
            ->whereDate('logged_at', Carbon::now())
            ->orderBy('logged_at', 'asc')
            ->get()
            ->groupBy(function($query) {
                return $query->logged_at->format('Y-m-d H');
            });


        $_yearlyMax = $this->calculateStatistics($_yearly, 'Y-m', 'max');
        $_yearlyMin = $this->calculateStatistics($_yearly, 'Y-m', 'min');
        $_yearlyAvg = $this->calculateStatistics($_yearly, 'Y-m', 'avg');

        $_monthlyMax = $this->calculateStatistics($_monthly, 'Y-m-d', 'max');
        $_monthlyMin = $this->calculateStatistics($_monthly, 'Y-m-d', 'min');
        $_monthlyAvg = $this->calculateStatistics($_monthly, 'Y-m-d', 'avg');

        $_dailyMax = $this->calculateStatistics($_daily, 'Y-m-d H', 'max');
        $_dailyMin = $this->calculateStatistics($_daily, 'Y-m-d H', 'min');
        $_dailyAvg = $this->calculateStatistics($_daily, 'Y-m-d H', 'avg');

        $yearlyMax = [];
        $yearlyMin = [];
        $yearlyAvg = [];

        $monthlyMax = [];
        $monthlyMin = [];
        $monthlyAvg = [];

        $dailyMax = [];
        $dailyMin = [];
        $dailyAvg = [];

        foreach ($_yearlyMax as $row) {
            $yearlyMax[] = $row;
        }
        foreach ($_yearlyMin as $row) {
            $yearlyMin[] = $row;
        }
        foreach ($_yearlyAvg as $row) {
            $yearlyAvg[] = $row;
        }

        foreach ($_monthlyMax as $row) {
            $monthlyMax[] = $row;
        }
        foreach ($_monthlyMin as $row) {
            $monthlyMin[] = $row;
        }
        foreach ($_monthlyAvg as $row) {
            $monthlyAvg[] = $row;
        }

        foreach ($_dailyMax as $row) {
            $dailyMax[] = $row;
        }
        foreach ($_dailyMin as $row) {
            $dailyMin[] = $row;
        }
        foreach ($_dailyAvg as $row) {
            $dailyAvg[] = $row;
        }


        $data->yearly_readings = (Object) [
            'max' => $yearlyMax,
            'min' => $yearlyMin,
            'avg' => $yearlyAvg
        ];
        $data->monthly_readings = (Object) [
            'max' => $monthlyMax,
            'min' => $monthlyMin,
            'avg' => $monthlyAvg
        ];
        $data->daily_readings = (Object) [
            'max' => $dailyMax,
            'min' => $dailyMin,
            'avg' => $dailyAvg
        ];

        return response()->json([
            'data' => $data
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSensorRequest $request, Sensor $sensor)
    {
        if (!$this->checkIfAllowed()) {
            return response()->json([
                'errors' => 'Not allowed'
            ], 402);
        }

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
        if (!$this->checkIfAllowed()) {
            return response()->json([
                'errors' => 'Not allowed'
            ], 402);
        }

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

    private function checkIfAllowed() {
        if (!User::find(Auth::user()->id)->isAdmin()) {
            return false;
        }

        return true;
    }
}
