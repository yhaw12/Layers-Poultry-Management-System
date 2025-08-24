<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    protected Client $client;
    protected string $apiKey;
    protected int $cacheTtl;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 6.0,
            'verify' => config('weather.openweather.verify_ssl', true),
        ]);

        $this->apiKey = config('weather.openweather.key') ?: env('OPENWEATHER_API_KEY', '');
        $this->cacheTtl = 10 * 60; // 10 minutes
    }

    // app/Services/WeatherService.php

public function getWeather($location = 'Kasoa,GH'): array
{
    if (empty($this->apiKey)) {
        return ['ok' => false, 'message' => 'Missing API key'];
    }

    $cacheKey = is_array($location)
        ? 'weather_' . md5($location['lat'] . '_' . $location['lon'])
        : 'weather_' . md5($location);

    return Cache::remember($cacheKey, $this->cacheTtl, function () use ($location) {
        try {
            $query = [
                'units' => 'metric',
                'appid' => $this->apiKey,
            ];

            if (is_array($location)) {
                $query['lat'] = $location['lat'];
                $query['lon'] = $location['lon'];
            } else {
                $query['q'] = $location;
            }

            $resp = $this->client->get('https://api.openweathermap.org/data/2.5/weather', [
                'query' => $query,
            ]);

            $json = json_decode((string) $resp->getBody(), true);

            if (isset($json['cod']) && (int) $json['cod'] === 200) {
                return [
                    'ok' => true,
                    'temperature' => (int) round($json['main']['temp']),
                    'condition' => $json['weather'][0]['description'] ?? '',
                    'location' => ($json['name'] ?? (is_array($location) ? 'Lat:'.$location['lat'].', Lon:'.$location['lon'] : $location)),
                    'raw' => $json,
                ];
            }

            return ['ok' => false, 'message' => $json['message'] ?? 'unknown'];
        } catch (\Throwable $e) {
            Log::warning('WeatherService error: ' . $e->getMessage());
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    });
}


    public function getWeatherByCoords(float $lat, float $lon): array
    {
        if (empty($this->apiKey)) {
            return ['ok' => false, 'message' => 'Missing API key'];
        }

        $cacheKey = 'weather_' . md5($lat . ',' . $lon);

        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($lat, $lon) {
            return $this->fetchWeather([
                'lat' => $lat,
                'lon' => $lon,
                'units' => 'metric',
            ], "$lat,$lon");
        });
    }

    protected function fetchWeather(array $query, string $fallbackLocation): array
    {
        try {
            $query['appid'] = $this->apiKey;
            $resp = $this->client->get('https://api.openweathermap.org/data/2.5/weather', [
                'query' => $query,
            ]);

            $json = json_decode((string) $resp->getBody(), true);

            if (isset($json['cod']) && (int)$json['cod'] === 200) {
                return [
                    'ok' => true,
                    'temperature' => (int) round($json['main']['temp']),
                    'condition' => $json['weather'][0]['description'] ?? '',
                    'location' => ($json['name'] ?? $fallbackLocation),
                    'raw' => $json,
                ];
            }

            return ['ok' => false, 'message' => $json['message'] ?? 'unknown'];
        } catch (\Throwable $e) {
            // Handle "offline" gracefully
            Log::warning('WeatherService error: ' . $e->getMessage());
            return ['ok' => false, 'message' => 'Offline or error: ' . $e->getMessage()];
        }
    }
}
