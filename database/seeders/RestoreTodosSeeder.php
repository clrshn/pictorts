<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RestoreTodosSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing todos
        DB::table('todos')->delete();
        
        // Insert the todo data
        $todos = [
            [
                'id' => 1,
                'title' => '2025 COMMITMENTS AND PAYABLES',
                'description' => 'Incoming/Outgoing documents to finish all pending transactions',
                'due_date' => null,
                'remarks' => null,
                'date_added' => '2026-02-02',
                'user_id' => 1,
                'assigned_to' => 'ADMIN UNIT',
                'created_at' => now(),
                'updated_at' => now(),
                'priority' => 'top',
                'status' => 'on-going',
            ],
            [
                'id' => 2,
                'title' => 'PDPFP',
                'description' => 'Input and review',
                'due_date' => null,
                'remarks' => null,
                'date_added' => '2026-02-02',
                'user_id' => 1,
                'assigned_to' => 'CLYDE',
                'created_at' => now(),
                'updated_at' => now(),
                'priority' => 'high',
                'status' => 'done',
            ],
            [
                'id' => 3,
                'title' => 'JPA - LUECO',
                'description' => 'Prepare requirements for JPA',
                'due_date' => null,
                'remarks' => null,
                'date_added' => '2026-02-02',
                'user_id' => 1,
                'assigned_to' => 'CLYDE',
                'created_at' => now(),
                'updated_at' => now(),
                'priority' => 'high',
                'status' => 'on-going',
            ],
            [
                'id' => 4,
                'title' => 'TECHNICAL - MEMO FOR APM',
                'description' => 'Prepare memo',
                'due_date' => null,
                'remarks' => null,
                'date_added' => '2026-02-02',
                'user_id' => 1,
                'assigned_to' => 'CLYDE',
                'created_at' => now(),
                'updated_at' => now(),
                'priority' => 'medium',
                'status' => 'done',
            ],
            [
                'id' => 5,
                'title' => 'ADMIN UNIT TRAINING - ASSESSMENT',
                'description' => 'Prepare assessment/justification',
                'due_date' => null,
                'remarks' => null,
                'date_added' => '2026-02-02',
                'user_id' => 1,
                'assigned_to' => 'CLYDE',
                'created_at' => now(),
                'updated_at' => now(),
                'priority' => 'high',
                'status' => 'pending',
            ],
            [
                'id' => 6,
                'title' => 'TECHNICAL - 2026 ICT CONSO',
                'description' => 'Check consolidation',
                'due_date' => null,
                'remarks' => null,
                'date_added' => '2026-02-02',
                'user_id' => 1,
                'assigned_to' => 'CLYDE',
                'created_at' => now(),
                'updated_at' => now(),
                'priority' => 'medium',
                'status' => 'on-going',
            ],
            [
                'id' => 7,
                'title' => 'UNDELIVERED ICT SUPPLIES 2025',
                'description' => 'Consolidate and follow up supplier deliveries',
                'due_date' => null,
                'remarks' => null,
                'date_added' => '2026-02-02',
                'user_id' => 1,
                'assigned_to' => 'MARGIE',
                'created_at' => now(),
                'updated_at' => now(),
                'priority' => 'medium',
                'status' => 'on-going',
            ],
            [
                'id' => 8,
                'title' => 'PREPARATION 2026 PURCHASE REQUESTS',
                'description' => 'Prepare 2026 PRs',
                'due_date' => null,
                'remarks' => null,
                'date_added' => '2026-02-02',
                'user_id' => 1,
                'assigned_to' => 'MARGIE',
                'created_at' => now(),
                'updated_at' => now(),
                'priority' => 'high',
                'status' => 'on-going',
            ],
        ];

        DB::table('todos')->insert($todos);
        
        $this->command->info('Restored ' . count($todos) . ' todo records successfully.');
    }
}
