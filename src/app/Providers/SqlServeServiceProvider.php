<?php

namespace App\Providers;

use App\Connection\SqlServerConnection;
use Illuminate\Database\Connection;
use Illuminate\Database\Connectors\Connector;
use Illuminate\Support\ServiceProvider;
//use Yajra\Oci8\Connectors\OracleConnector as Connector;


class SqlServeServiceProvider extends ServiceProvider
{

    /**
    * Indicates if loading of the provider is deferred.
    *
    * @var bool
    */
    protected $defer = false;

    /**
     * Boot Oci8 Provider.
     */
    public function boot()
    {
//        $this->publishes([
//            __DIR__ . '/../config/oracle.php' => config_path('oracle.php'),
//        ], 'oracle');
//
//        Auth::provider('oracle', function ($app, array $config) {
//            return new OracleUserProvider($app['hash'], $config['model']);
//        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
//        if (file_exists(config_path('oracle.php'))) {
//            $this->mergeConfigFrom(config_path('oracle.php'), 'database.connections');
//        } else {
//            $this->mergeConfigFrom(__DIR__ . '/../config/oracle.php', 'database.connections');
//        }

        Connection::resolverFor('sqlsrv', function ($connection, $database, $prefix, $config) {
            $db = new SqlServerConnection($connection, $database, $prefix, $config);

            if (! empty($config['skip_session_vars'])) {
                return $db;
            }

            return $db;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [];
    }
}
