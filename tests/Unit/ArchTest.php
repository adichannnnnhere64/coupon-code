<?php

declare(strict_types=1);

arch()->preset()->php();
arch()->preset()->strict();
arch()->preset()->security();
/* ->expect('Database\Seeders') */
/* ->not->toBeUsed(); */

arch('controllers')
    ->expect('App\Http\Controllers')
    ->not->toBeUsed();

//
