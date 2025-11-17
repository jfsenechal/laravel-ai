<?php

use App\Mcp\Servers\WeatherServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::oauthRoutes();

Mcp::web('/mcp/weather', WeatherServer::class)
    ->middleware('auth:api');

//perfect for building local AI assistant integrations like Laravel Boost.
Mcp::local('weather', WeatherServer::class);
