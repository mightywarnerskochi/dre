<?php

use Illuminate\Support\Facades\Route;

Route::view('/{any?}', 'app', ['styleVersion' => '14'])->where('any', '(?!api|sanctum).*$');
