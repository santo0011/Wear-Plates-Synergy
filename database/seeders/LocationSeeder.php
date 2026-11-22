<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

/**
 * Offices from the company profile (PDF pages 1, 3, 4, 19, 21, 52). Addresses are filled only where the
 * profile gives a full address. Coordinates are city centres, used to place pins on the India map.
 * Existing locations are kept; only empty addresses are filled in.
 */
class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            ['Mumbai', 'Maharashtra', 'corporate', 19.0760, 72.8777, 'Palladio Building, 702, Hiranandani Estate, GB Road, Thane (W) 400607'],
            ['Ankleshwar (GIDC)', 'Gujarat', 'plant', 21.6264, 73.0152, 'Plot No. 4311, Near Crystal Chowkdi (BSNL Tower), GIDC, Ankleshwar 393002, Dist. Bharuch'],
            ['Ahmedabad', 'Gujarat', 'regional', 23.0225, 72.5714, null],
            ['Kolkata', 'West Bengal', 'regional', 22.5726, 88.3639, 'Lotus Park, 58 Regent Estate, Shree Colony Road, Kolkata 700047'],
            ['Hyderabad', 'Telangana', 'regional', 17.3850, 78.4867, 'Gautam Nagar, H. No. 19-37, Malkajgiri, Secunderabad 500047'],
            ['Guwahati', 'Assam', 'regional', 26.1445, 91.7362, null],
            ['Palampur', 'Himachal Pradesh', 'regional', 32.1109, 76.5363, null],
            ['Jamshedpur', 'Jharkhand', 'regional', 22.8046, 86.2029, null],
            ['Noida', 'Uttar Pradesh', 'regional', 28.5355, 77.3910, null],
            ['Prayagraj', 'Uttar Pradesh', 'regional', 25.4358, 81.8463, null],
            ['Chittorgarh', 'Rajasthan', 'regional', 24.8887, 74.6269, null],
            ['Udaipur', 'Rajasthan', 'regional', 24.5854, 73.7125, 'Upali Badi, Bari, P.O. Badgaon, Udaipur 313011'],
        ];

        foreach ($locations as $i => [$city, $state, $type, $lat, $lng, $address]) {
            $location = Location::firstOrCreate(['city' => $city, 'type' => $type], [
                'state' => $state,
                'address' => $address,
                'latitude' => $lat,
                'longitude' => $lng,
                'sort_order' => $i,
                'is_active' => true,
            ]);
            if ($address && blank($location->address)) {
                $location->update(['address' => $address]);
            }
        }
    }
}
