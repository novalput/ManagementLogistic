<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Item;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use Carbon\Carbon;

class DashboardSeeder extends Seeder
{
    public function run()
    {
        // Create Admin/Users if not exist
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin User', 'password' => bcrypt('password'), 'role' => 'admin']
        );

        $supervisor = User::firstOrCreate(
            ['email' => 'supervisor@example.com'],
            ['name' => 'Supervisor User', 'password' => bcrypt('password'), 'role' => 'supervisor']
        );

        // 1. Available Items: Need 20.
        $spidol = Item::create([
            'item_code' => 'ITM001',
            'item_name' => 'Spidol',
            'type' => 'consumable',
            'stock' => 20,
            'condition' => 'good'
        ]);
        
        // 2. Defective Items: 2
        $map = Item::create([
            'item_code' => 'ITM002',
            'item_name' => 'Map',
            'type' => 'consumable',
            'stock' => 2,
            'condition' => 'defect'
        ]);

        // 3. Borrowed Items: 9
        $kertas = Item::create([
            'item_code' => 'ITM003',
            'item_name' => 'Kertas A4',
            'type' => 'consumable',
            'stock' => 0, 
            'condition' => 'good'
        ]);

        // Create Borrowing Record (format RDM-YYYY-xxxxx)
        $year = Carbon::now()->year;
        $lastBorrowing = Borrowing::where('borrowing_code', 'like', "RDM-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();
        $nextSeq = $lastBorrowing
            ? (int) substr($lastBorrowing->borrowing_code, -5) + 1
            : 1;
        $borrowingCode = 'RDM-' . $year . '-' . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);

        $borrowing = Borrowing::create([
            'borrowing_code' => $borrowingCode,
            'borrower_id' => $supervisor->id,
            'borrower_name' => $supervisor->name,
            'borrower_division' => '',
            'borrow_date' => Carbon::now(),
            'due_date' => null, // Consumables do not have a due date
            'purpose' => 'Dashboard sample data',
            'notes' => 'Seeded record for dashboard preview',
            'status' => 'borrowed',
            'requested_at' => Carbon::now()->subHours(2),
            'authorized_by' => $admin->id,
            'authorized_at' => Carbon::now()->subHour(),
        ]);

        BorrowingDetail::create([
            'borrowing_id' => $borrowing->id,
            'item_id' => $kertas->id,
            'quantity' => 9,
        ]);
    }
}
