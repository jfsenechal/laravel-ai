<?php

namespace App\Mcp\Servers;

use App\Mcp\Prompts\DescribeWeatherPrompt;
use App\Mcp\Resources\WeatherGuidelinesResource;
use App\Mcp\Tools\CurrentWeatherTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Tool;

class WeatherServer extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name = 'Weather Server';

    /**
     * The MCP server's version.
     */
    protected string $version = '0.0.1';

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions = 'This server provides weather information and forecasts.';

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int,Tool>
     */
    protected array $tools = [
        CurrentWeatherTool::class,
    ];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int,Resource>
     */
    protected array $resources = [
        WeatherGuidelinesResource::class,
    ];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int,Prompt>
     */
    protected array $prompts = [
        DescribeWeatherPrompt::class,
    ];
}
