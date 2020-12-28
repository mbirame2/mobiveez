<?php
namespace providers;

use RuntimeException;

use Illuminate\Hashing\HasherInterface;
use App\Services\SendEmailService;
use Illuminate\Hashing\HashServiceProvider;

class SHAHashServiceProvider extends HashServiceProvider{

/**
 * Register the service provider.
 *
 * @return void
 */
public function register() {
    $this->app['hash'] = $this->app->share(function () {
        return new SHAHasher();
    });

}

/**
 * Get the services provided by the provider.
 *
 * @return array
 */
public function provides() {
    return array('hash');
}

}