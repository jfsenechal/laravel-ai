<?php

use App\Mcp\Servers\WeatherServer;
use Laravel\Mcp\Facades\Mcp;

// Mcp::web('/mcp/demo', \App\Mcp\Servers\PublicServer::class);

Mcp::web('/mcp/weather', WeatherServer::class);

//perfect for building local AI assistant integrations like Laravel Boost.
//Mcp::local('weather', WeatherServer::class);
