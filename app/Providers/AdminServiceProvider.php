<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\DesignerMiddleware;

class AdminServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(Kernel $kernel): void
    {
        // Register middleware aliases
    //     $kernel->alias('admin', AdminMiddleware::class);
    //     $kernel->alias('designer', DesignerMiddleware::class);
    // }
}
}