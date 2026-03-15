<?php

namespace Database\Seeders;

use App\Models\ReportReason;
use Illuminate\Database\Seeder;

class ReportReasonSeeder extends Seeder
{
    public function run(): void
    {
        $reasons = [
            ['name' => 'Neprimerna vsebina', 'sort_order' => 1],
            ['name' => 'Avtorske pravice', 'sort_order' => 2],
            ['name' => 'Napačna kategorija', 'sort_order' => 3],
            ['name' => 'Spam ali oglaševanje', 'sort_order' => 4],
            ['name' => 'Drugo', 'sort_order' => 5],
        ];

        ReportReason::upsert(
            $reasons,
            ['sort_order'],
            ['name']
        );
    }
}
