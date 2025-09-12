protected $middlewareAliases = [
    'auth' => \App\Http\Middleware\Authenticate::class,
    'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
    // ... other middleware aliases
    'admin' => \App\Http\Middleware\AdminMiddleware::class,
    'designer' => \App\Http\Middleware\DesignerMiddleware::class,
];