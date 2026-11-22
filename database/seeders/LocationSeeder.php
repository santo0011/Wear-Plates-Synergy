<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

/**
 * Offices from the company profile. Only city/state are stored; full addresses are left
 * blank to be added from the admin panel once verified. Coordinates are city centres,
 * used solely to place pins on the India map.
 */
class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['Mumbai', 'Maharashtra', 'corporate', 19.0760, 72.8777],
            ['Ankleshwar (GIDC)', 'Gujarat', 'plant', 21.6264, 73.0152],
            ['Ahmedabad', 'Gujarat', 'regional', 23.0225, 72.5714],
            ['Kolkata', 'West Bengal', 'regional', 22.5726, 88.3639],
            ['Hyderabad', 'Telangana', 'regional', 17.3850, 78.4867],
            ['Guwahati', 'Assam', 'regional', 26.1445, 91.7362],
            ['Palampur', 'Himachal Pradesh', 'regional', 32.1109, 76.5363],
            ['Jamshedpur', 'Jharkhand', 'regional', 22.8046, 86.2029],
            ['Noida', 'Uttar Pradesh', 'regional', 28.5355, 77.3910],
            ['Prayagraj', 'Uttar Pradesh', 'regional', 25.4358, 81.8463],
            ['Chittorgarh', 'Rajasthan', 'regional', 24.8887, 74.6269],
        ];

        foreach ($locations as $i => [$city, $state, $type, $lat, $lng]) {
            Location::firstOrCreate(['city' => $city, 'type' => $type], [
                'state' => $state,
                'latitude' => $lat,
                'longitude' => $lng,
                'sort_order' => $i,
                'is_active' => true,
            ]);
        }
    }
}
