<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::query()->firstOrCreate(
            ['email' => 'admin@lightwavecrm.com'],
            [
                'name' => 'Admin',
                'role' => 'admin',
                'password' => 'admin123456',
                'email_verified_at' => now(),
            ]
        );

        $agent = User::query()->firstOrCreate(
            ['email' => 'agent@lightwavecrm.com'],
            [
                'name' => 'Support Agent',
                'role' => 'agent',
                'password' => 'agent123456',
                'email_verified_at' => now(),
            ]
        );

        $client = Client::query()->firstOrCreate(
            ['phone' => '201000000001'],
            [
                'full_name' => 'Demo Client',
                'email' => 'client@example.com',
                'service_plan' => 'Fiber 100 Mbps',
                'status' => 'active',
                'address' => 'Sample Address',
                'notes' => 'Seeded demo client',
            ]
        );

        Ticket::query()->firstOrCreate(
            ['ticket_no' => 'TKT-' . now()->format('Ymd') . '-0001'],
            [
                'client_id' => $client->id,
                'opened_by' => $admin->id,
                'assigned_to' => $agent->id,
                'subject' => 'Demo Ticket - Slow Internet',
                'description' => 'Customer reports unstable speed in evening hours.',
                'priority' => 'high',
                'status' => 'open',
                'source' => 'phone',
            ]
        );
    }
}
