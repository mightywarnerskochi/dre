<?php

define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

use App\Models\CmsKit\HomeBannerFilterDefinition;
use Illuminate\Contracts\Console\Kernel;

$app->make(Kernel::class)->bootstrap();

$def = HomeBannerFilterDefinition::where('key', 'location')->first();
if ($def) {
    $def->source_column = 'city,community,address,title';
    $def->save();
    echo "Location filter definition updated with title.\n";
} else {
    echo "Location filter definition not found.\n";
}
