<?php
namespace Uzzal\ApiToken;

use Illuminate\Support\ServiceProvider;

/**
 *
 * @author Mahabubul Hasan <codehasan@gmail.com>
 */
class TokenServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }
}