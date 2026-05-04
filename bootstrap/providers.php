<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    App\Providers\RepositoryServiceProvider::class,
    \Intervention\Image\ImageServiceProvider::class,
];
