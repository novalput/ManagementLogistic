<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$borrowingCount = App\Models\Borrowing::where('status', 'borrowed')->count();
echo "Active Borrowing Transactions: " . $borrowingCount . PHP_EOL;

$borrowedItemsSum = App\Models\BorrowingDetail::whereHas('borrowing', function($q) {
    $q->where('status', 'borrowed');
})->sum('quantity');
echo "Total Borrowed Items Quantity: " . $borrowedItemsSum . PHP_EOL;

$details = App\Models\BorrowingDetail::whereHas('borrowing', function($q) {
    $q->where('status', 'borrowed');
})->with(['item', 'borrowing'])->get();

echo "Details:" . PHP_EOL;
foreach($details as $d) {
    echo "- " . $d->item->item_name . " (Qty: " . $d->quantity . ") - Borrowing Code: " . $d->borrowing->borrowing_code . PHP_EOL;
}
