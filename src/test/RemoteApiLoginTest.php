<?php

namespace Wergh\RemoteApiLogin\Tests;

use Orchestra\Testbench\TestCase;
use Wergh\RemoteApiLogin\RemoteApiLoginServiceProvider;

class RemoteApiLoginTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [RemoteApiLoginServiceProvider::class];
    }

    /** @test */
    public function it_can_login()
    {
        // Implementa tus pruebas aquí
        $this->assertTrue(true);
    }
}
