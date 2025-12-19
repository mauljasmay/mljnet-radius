<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Users
            UserSeeder::class,

            // Integration Settings
            IntegrationSettingsSeeder::class,

            // WhatsApp Templates
            WhatsAppTemplateSeeder::class,
        ]);
    }
}
