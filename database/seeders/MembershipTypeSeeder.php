<?php

namespace Database\Seeders;

use App\Models\MembershipType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MembershipTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Annual Regular Member',
                'description' => 'Standard annual membership for regular members.',
                'annual_amount' => 1200.00,
                'monthly_amount' => null, // Or 100.00 if you offer monthly option
                'is_recurring' => true,
                'membership_duration' => '12 months',
                'is_active' => true,
            ],
            [
                'name' => 'Annual Student Member',
                'description' => 'Discounted annual membership for students.',
                'annual_amount' => 600.00,
                'monthly_amount' => null, // Or 50.00
                'is_recurring' => true,
                'membership_duration' => '12 months',
                'is_active' => true,
            ],
            [
                'name' => 'Lifetime Patron Member',
                'description' => 'One-time payment for lifetime patronage.',
                'annual_amount' => 10000.00, // Or a specific 'lifetime_amount' field if you prefer
                'monthly_amount' => null,
                'is_recurring' => false,
                'membership_duration' => 'Lifetime',
                'is_active' => true,
            ],
            [
                'name' => 'Free Basic Access', // Example of a free tier
                'description' => 'Basic access to some resources without a fee.',
                'annual_amount' => 0.00,
                'monthly_amount' => 0.00,
                'is_recurring' => false,
                'membership_duration' => '12 months', // Or lifetime if applicable
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            MembershipType::firstOrCreate(
                ['slug' => Str::slug($type['name'])],
                $type
            );
        }
        $this->command->info(count($types) . ' membership types seeded.');
    }
}
