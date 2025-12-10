<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$pdo = Illuminate\Support\Facades\DB::select('select reference_type, count(*) as cnt from stock_movements group by reference_type order by cnt desc');
foreach($pdo as $row) {
    echo ($row->reference_type ?? 'NULL') . " => {$row->cnt}\n";
}
