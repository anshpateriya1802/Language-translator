<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'name' => 'English',
                'code' => 'en',
                'native_name' => 'English',
                'is_active' => true,
            ],
            [
                'name' => 'Spanish',
                'code' => 'es',
                'native_name' => 'Español',
                'is_active' => true,
            ],
            [
                'name' => 'French',
                'code' => 'fr',
                'native_name' => 'Français',
                'is_active' => true,
            ],
            [
                'name' => 'German',
                'code' => 'de',
                'native_name' => 'Deutsch',
                'is_active' => true,
            ],
            [
                'name' => 'Italian',
                'code' => 'it',
                'native_name' => 'Italiano',
                'is_active' => true,
            ],
            [
                'name' => 'Portuguese',
                'code' => 'pt',
                'native_name' => 'Português',
                'is_active' => true,
            ],
            [
                'name' => 'Russian',
                'code' => 'ru',
                'native_name' => 'Русский',
                'is_active' => true,
            ],
            [
                'name' => 'Japanese',
                'code' => 'ja',
                'native_name' => '日本語',
                'is_active' => true,
            ],
            [
                'name' => 'Chinese (Simplified)',
                'code' => 'zh-CN',
                'native_name' => '简体中文',
                'is_active' => true,
            ],
            [
                'name' => 'Arabic',
                'code' => 'ar',
                'native_name' => 'العربية',
                'is_active' => true,
            ],
            [
                'name' => 'Hindi',
                'code' => 'hi',
                'native_name' => 'हिन्दी',
                'is_active' => true,
            ],
        ];

        foreach ($languages as $language) {
            Language::create($language);
        }
    }
} 