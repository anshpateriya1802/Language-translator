<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Region;
use App\Models\Language;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get language IDs
        $english = Language::where('code', 'en')->first();
        $spanish = Language::where('code', 'es')->first();
        $french = Language::where('code', 'fr')->first();
        $portuguese = Language::where('code', 'pt')->first();

        $regions = [
            // English regions
            [
                'name' => 'American English',
                'code' => 'en-US',
                'language_id' => $english->id,
                'country' => 'United States',
                'description' => 'American variant of English language',
                'is_active' => true,
            ],
            [
                'name' => 'British English',
                'code' => 'en-GB',
                'language_id' => $english->id,
                'country' => 'United Kingdom',
                'description' => 'British variant of English language',
                'is_active' => true,
            ],
            [
                'name' => 'Australian English',
                'code' => 'en-AU',
                'language_id' => $english->id,
                'country' => 'Australia',
                'description' => 'Australian variant of English language',
                'is_active' => true,
            ],
            [
                'name' => 'Indian English',
                'code' => 'en-IN',
                'language_id' => $english->id,
                'country' => 'India',
                'description' => 'Indian variant of English language',
                'is_active' => true,
            ],
            
            // Spanish regions
            [
                'name' => 'Spain Spanish',
                'code' => 'es-ES',
                'language_id' => $spanish->id,
                'country' => 'Spain',
                'description' => 'Spanish as spoken in Spain',
                'is_active' => true,
            ],
            [
                'name' => 'Mexican Spanish',
                'code' => 'es-MX',
                'language_id' => $spanish->id,
                'country' => 'Mexico',
                'description' => 'Spanish as spoken in Mexico',
                'is_active' => true,
            ],
            [
                'name' => 'Argentinian Spanish',
                'code' => 'es-AR',
                'language_id' => $spanish->id,
                'country' => 'Argentina',
                'description' => 'Spanish as spoken in Argentina',
                'is_active' => true,
            ],
            
            // French regions
            [
                'name' => 'France French',
                'code' => 'fr-FR',
                'language_id' => $french->id,
                'country' => 'France',
                'description' => 'French as spoken in France',
                'is_active' => true,
            ],
            [
                'name' => 'Canadian French',
                'code' => 'fr-CA',
                'language_id' => $french->id,
                'country' => 'Canada',
                'description' => 'French as spoken in Canada',
                'is_active' => true,
            ],
            [
                'name' => 'Belgian French',
                'code' => 'fr-BE',
                'language_id' => $french->id,
                'country' => 'Belgium',
                'description' => 'French as spoken in Belgium',
                'is_active' => true,
            ],
            
            // Portuguese regions
            [
                'name' => 'Portugal Portuguese',
                'code' => 'pt-PT',
                'language_id' => $portuguese->id,
                'country' => 'Portugal',
                'description' => 'Portuguese as spoken in Portugal',
                'is_active' => true,
            ],
            [
                'name' => 'Brazilian Portuguese',
                'code' => 'pt-BR',
                'language_id' => $portuguese->id,
                'country' => 'Brazil',
                'description' => 'Portuguese as spoken in Brazil',
                'is_active' => true,
            ],
        ];

        foreach ($regions as $region) {
            Region::create($region);
        }
    }
} 