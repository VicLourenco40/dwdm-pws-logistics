<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            [
                'title' => 'Pending',
                'slug' => 'pending'
            ],
            [
                'title' => 'Available for distribution',
                'slug' => 'available-for-distribution'
            ],
            [
                'title' => 'In transit',
                'slug' => 'in-transit'
            ],
            [
                'title' => 'Delivered',
                'slug' => 'delivered'
            ],
            [
                'title' => 'Returned',
                'slug' => 'returned'
            ],
            [
                'title' => 'Cancelled',
                'slug' => 'cancelled'
            ]
        ];

        foreach($statuses as $status) {
            Status::create($status);
        }
    }
}
