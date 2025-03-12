<?php

use App\Controllers\HomeController;
use App\Controllers\ApiController;
use App\Services\CacheService;
use App\Services\DatabaseService;
use App\Services\RuneScapeService;
use DI\ContainerBuilder;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use function DI\autowire;
use function DI\get;
use function DI\create;

// Initialize database connection early, before any container setup
// This is critical to prevent the "Call to a member function connection() on null" error
$databaseFile = __DIR__ . '/../database/database.sqlite';
if (!file_exists(dirname($databaseFile))) {
    mkdir(dirname($databaseFile), 0755, true);
}
if (!file_exists($databaseFile)) {
    touch($databaseFile);
    chmod($databaseFile, 0644);
}

$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => $databaseFile,
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Ensure the table exists
if (!$capsule::schema()->hasTable('status_checks')) {
    $capsule::schema()->create('status_checks', function ($table) {
        $table->increments('id');
        $table->string('current_status');
        $table->json('maintenance_data')->nullable();
        $table->timestamps();  // This creates both created_at and updated_at
    });
}

// Now build the container
$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    // Configuration
    'settings' => [
        'displayErrorDetails' => true,
        'source_url' => 'https://secure.runescape.com/m=news/game-status-information-centre?oldschool=1',
        'check_interval' => getenv('CHECK_INTERVAL') ?: 180, // 3 minutes default
        'max_history' => getenv('MAX_HISTORY') ?: 10,
    ],

    // Cache
    'cache' => function() {
        $cacheDir = __DIR__ . '/../cache';
        if (!file_exists($cacheDir)) {
            mkdir($cacheDir, 0755, true);
        }
        return new FilesystemAdapter('runescape', 0, $cacheDir);
    },

    // Services - using explicit creation with parameters
    DatabaseService::class => autowire(),
    CacheService::class => autowire(),

    // Explicitly passing settings to RuneScapeService
    RuneScapeService::class => function($c) {
        return new RuneScapeService(
            $c->get(CacheService::class),
            $c->get(DatabaseService::class),
            $c->get('settings')
        );
    },

    // Controllers with explicit settings parameter
    HomeController::class => function($c) {
        return new HomeController(
            $c->get(RuneScapeService::class),
            $c->get('settings')
        );
    },

    ApiController::class => autowire(),
]);

return $containerBuilder->build();