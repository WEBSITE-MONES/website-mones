<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class WeatherController extends Controller
{
    public function getWeather(Request $request)
    {
        $lat = $request->query('lat');
        $lon = $request->query('lon');

        if (!$lat || !$lon) {
            return response()->json(['error' => 'Latitude & Longitude required'], 400);
        }

        $cacheKey = "weather_{$lat}_{$lon}";

        // Simpan hasil cuaca selama 10 menit
        $weather = Cache::remember($cacheKey, 600, function () use ($lat, $lon) {
            $apiKey = config('services.openweather.key'); 
            $url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&units=metric&lang=id&appid={$apiKey}";

            try {
                $response = Http::timeout(10)
                    ->withOptions([
                        'verify' => false, // kalau di XAMPP/localhost SSL error → bypass
                    ])
                    ->get($url);

                if ($response->successful()) {
                    $json = $response->json();

                    return [
                        'temperature' => $json['main']['temp'] ?? 0,
                        'description' => ucfirst($json['weather'][0]['description'] ?? 'Tidak ada data'),
                        'humidity'    => $json['main']['humidity'] ?? 0,
                        'icon'        => $json['weather'][0]['icon'] ?? '01d',
                    ];
                }

                Log::error("Weather API gagal", [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

            } catch (\Exception $e) {
                Log::error("Weather API Exception: " . $e->getMessage());
            }

            return [
                'temperature' => 0,
                'description' => 'Data cuaca tidak tersedia',
                'humidity'    => 0,
                'icon'        => '01d',
            ];
        });

        return response()->json($weather);
    }
}