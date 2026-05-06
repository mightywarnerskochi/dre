<?php

define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use App\Models\CmsKit\HomeBannerFilterValue;
use Illuminate\Contracts\Console\Kernel;

$app->make(Kernel::class)->bootstrap();

$v = HomeBannerFilterValue::where('value', 'LIKE', '%omnis%')->first();
if ($v) {
    echo "Found: " . $v->value . "\n";
    print_r($v->translations);
} else {
    echo "Not found.\n";
}
