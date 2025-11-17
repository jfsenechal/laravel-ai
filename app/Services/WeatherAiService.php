<?php

namespace App\Services;

use App\Repository\BottinRepository;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Tool;

class WeatherAiService
{
    public function __construct(
        protected BottinRepository $repository,
    ) {}

    /**
     * Get AI-powered weather response with tool calling.
     */
    public function getWeatherResponse(string $userMessage): string
    {
        // Define the weather tool for OpenAI function calling
        $weatherTool = Tool::as('get_current_weather')
            ->for('Get the current weather forecast for a specified location')
            ->withParameter('location', 'The location to get the weather for (e.g., "New York City", "Tokyo")', 'string', true)
            ->withParameter('units', 'The temperature units to use', 'string', false, ['celsius', 'fahrenheit'])
            ->using($this->executeWeatherTool(...));

        // Call OpenAI with the tool
        $response = Prism::text()
            ->using(Provider::OpenAI, 'gpt-4')
            ->withPrompt($userMessage)
            ->withTools([$weatherTool])
            ->withMaxSteps(5) // Allow multiple tool calls
            ->generate();

        return $response->text;
    }

    /**
     * Execute the weather tool when called by OpenAI.
     */
    protected function executeWeatherTool(string $location, ?string $units = 'celsius'): string
    {
        // Use the repository method (same as MCP tool)
        $forecast = $this->repository->getForecastFor($location);

        if (! $forecast) {
            return 'Unable to fetch weather data. Please try again.';
        }

        // You could also convert units here if needed
        $unitSymbol = $units === 'fahrenheit' ? '°F' : '°C';

        return $forecast; // Or format as needed: "Weather in {$location}: {$forecast}"
    }

    /**
     * Example: Simple weather query without tool calling.
     */
    public function simpleWeatherQuery(string $location): string
    {
        $forecast = $this->repository->getForecastFor($location);

        $response = Prism::text()
            ->using(Provider::OpenAI, 'gpt-4')
            ->withPrompt("Based on this weather data: {$forecast}, provide a friendly weather summary for {$location}")
            ->generate();

        return $response->text;
    }

    /**
     * Example: Get structured data from weather info.
     */
    public function getStructuredWeather(string $location): array
    {
        $weatherTool = Tool::as('get_current_weather')
            ->for('Get weather forecast')
            ->withParameter('location', 'Location name', 'string', true)
            ->using(fn (string $loc) => $this->repository->getForecastFor($loc));

        $response = Prism::text()
            ->using(Provider::OpenAI, 'gpt-4')
            ->withPrompt("What's the weather in {$location}? Provide temperature, conditions, and recommendation.")
            ->withTools([$weatherTool])
            ->withMaxSteps(3)
            ->generate();

        return [
            'location' => $location,
            'summary' => $response->text,
            'tool_calls' => $response->steps->map(fn ($step) => $step->toolCalls)->flatten()->toArray(),
        ];
    }
}
