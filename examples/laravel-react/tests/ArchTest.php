<?php

// Consumer project tests/ArchTest.php: the shared arch preset plus any
// project-specific expectations.
require_once base_path('vendor/scrapkit/engineering-kit/configs/testing/pest.php');

scrapkit_arch_preset();

arch('controllers stay thin')
    ->expect('App\Http\Controllers')
    ->not->toUse('Illuminate\Support\Facades\DB');
