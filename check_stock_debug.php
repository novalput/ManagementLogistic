<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$itemCode = 'ITM003';
$item = App\Models\Item::where('item_code', $itemCode)->first();

if (!$item) {
    echo "Item $itemCode not found\n";
    exit;
}

echo "Item: " . $item->item_name . "\n";
echo "Aggregated Stock: " . $item->aggregated_stock . "\n";
echo "Units Available: " . $item->units()->where('status', 'available')->where('condition', 'good')->count() . "\n";
echo "Units Total: " . $item->units()->count() . "\n";

echo "\nUnit Details:\n";
$units = $item->units()->get();
foreach($units as $u) {
    echo "- " . $u->serial_number . ": " . $u->status . " (" . $u->condition . ")\n";
}
