<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use App\Models\StockMovement;
$sm = StockMovement::where('reference_type','restock')->first();
if (!$sm) { echo "No restock movements\n"; exit; }
echo "Found movement id={$sm->id} reference_type={$sm->reference_type}\n";
try {
    $ref = $sm->reference;
    if ($ref) {
        echo 'Reference class: ' . get_class($ref) . " id={$ref->id}\n";
    } else {
        echo "Reference is null\n";
    }
} catch (Throwable $e) {
    echo 'Error: '.$e->getMessage()."\n";
}
