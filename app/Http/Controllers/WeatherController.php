<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    protected WeatherService $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->middleware('auth');
        $this->weatherService = $weatherService;
    }

    public function fetch(Request $request)
    {
        $lat = $request->input('lat');
        $lon = $request->input('lon');
        $q = $request->input('q');

        if ($lat && $lon) {
            $data = $this->weatherService->getWeatherByCoords((float)$lat, (float)$lon);
        } else {
            $data = $this->weatherService->getWeather($q ?? 'Kasoa,GH');
        }

        return response()->json($data);
    }
}
