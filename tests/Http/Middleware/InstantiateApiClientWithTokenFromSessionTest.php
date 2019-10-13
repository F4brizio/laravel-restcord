<?php

namespace LaravelRestcord;

use GuzzleHttp\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Session\Session;
use LaravelRestcord\Discord\ApiClient;
use LaravelRestcord\Http\Middleware\InstantiateApiClientWithTokenFromSession;
use Mockery;
use PHPUnit\Framework\TestCase;

class InstantiateApiClientWithTokenFromSessionTest extends TestCase
{
    /** @var Mockery\MockInterface */
    protected $session;

    /** @var Mockery\MockInterface */
    protected $application;

    /** @var InstantiateApiClientWithTokenFromSession */
    protected $middleware;

    public function setUp(): void
    {
        parent::setUp();

        $this->session = Mockery::mock(Session::class);
        $this->application = Mockery::mock(Application::class);

        $this->middleware = new InstantiateApiClientWithTokenFromSession($this->session, $this->application);
    }

    /** @test */
    public function setsClientWithTokenForDiscordWhenPresentInSession()
    {
        $client = Mockery::mock(ApiClient::class);

        $this->application->shouldReceive('make')->with(ApiClient::class)->andReturn($client);

        $this->session->shouldReceive('has')->with('discord_token')->andReturn(true);

        $this->assertEquals(1, $this->middleware->handle('', function () {
            return 1;
        }));

        $this->assertEquals($client, Discord::client());
    }

    /** @test */
    public function doesntSetClientWhenNotPresentInDiscord()
    {
        $client = Mockery::mock(ApiClient::class);

        $this->application->shouldReceive('make')->with(ApiClient::class)->andReturn($client)->never();

        $this->session->shouldReceive('has')->with('discord_token')->andReturn(false);

        $this->assertEquals(1, $this->middleware->handle('', function () {
            return 1;
        }));
    }
}
