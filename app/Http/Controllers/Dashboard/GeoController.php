<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class GeoController extends Controller
{
    public function getLocation(Request $request)
    {
        $cacheKey = "user_location_default";
        $loc = Cache::remember($cacheKey, 3600, function () {
            try {
                $response = Http::timeout(5)
                    ->withOptions(['verify' => false])
                    ->get("https://ipapi.co/json/");

                if ($response->successful()) {
                    $json = $response->json();

                    if (isset($json['error'])) {
                        return $this->getFallbackLocation();
                    }

                    return [
                        'city'      => $json['city'] ?? 'Makassar',
                        'region'    => $json['region'] ?? 'Sulawesi Selatan',
                        'country'   => $json['country_name'] ?? 'Indonesia',
                        'latitude'  => $json['latitude'] ?? -5.1477,
                        'longitude' => $json['longitude'] ?? 119.4327,
                    ];
                }
            } catch (\Exception $e) {
            }

            return $this->getFallbackLocation();
        });

        return response()->json($loc);
    }

    private function getFallbackLocation()
    {
        return [
            'city'      => 'Makassar',
            'region'    => 'Sulawesi Selatan',
            'country'   => 'Indonesia',
            'latitude'  => -5.1477,
            'longitude' => 119.4327,
        ];
    }
}