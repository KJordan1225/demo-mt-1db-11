<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Stancl\Tenancy\Database\Models\Tenant;

class DemoTenantsSeeder extends Seeder
{
    public function run(): void
    {
        // alpha
        Tenant::query()->firstOrCreate(
            ['id' => 'alpha'],
            ['data' => [
                'display_name' => 'Alpha Fitness',
                'branding' => [
                    'logo_url'      => 'https://via.placeholder.com/64x64.png?text=A',
                    'primary_color' => '#6d28d9', // violet-700
                    'accent_color'  => '#f59e0b', // amber-500
                    'bg_color'      => '#f5f3ff', // violet-50
                    'text_color'    => '#111827',
                ],
            ]]
        );

        // bravo
        Tenant::query()->firstOrCreate(
            ['id' => 'bravo'],
            ['data' => [
                'display_name' => 'Bravo Studios',
                'branding' => [
                    'logo_url'      => 'https://via.placeholder.com/64x64.png?text=B',
                    'primary_color' => '#0ea5e9', // sky-500
                    'accent_color'  => '#10b981', // emerald-500
                    'bg_color'      => '#ecfeff', // cyan-50
                    'text_color'    => '#0f172a',
                ],
            ]]
        );
    }
}
