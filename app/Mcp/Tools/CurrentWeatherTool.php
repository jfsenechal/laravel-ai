<?php

namespace App\Mcp\Tools;

use App\Repository\BottinRepository;
use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;
use Laravel\Mcp\Server\Tools\Annotations\IsIdempotent;
use Laravel\Mcp\Server\Tools\Annotations\IsReadOnly;

#[IsIdempotent]
#[IsReadOnly]
class CurrentWeatherTool extends Tool
{
    /**
     * Create a new tool instance.
     */
    public function __construct(
        protected BottinRepository $weather,
    ) {
    }

    /**
     * The tool's name.
     */
    protected string $name = 'get-optimistic-weather';

    /**
     * The tool's title.
     */
    protected string $title = 'Get Optimistic Weather Forecast';

    /**
     * The tool's description.
     */
    protected string $description = 'Fetches the current weather forecast for a specified location.';

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        if (!$request->user()->can('read-weather')) {
          //  return Response::error('Permission denied.');
        }
        $validated = $request->validate([
            'location' => ['required', 'string', 'max:100'],
            'units' => 'in:celsius,fahrenheit',
        ], [
            'location.required' => 'You must specify a location to get the weather for. For example, "New York City" or "Tokyo".',
            'units.in' => 'You must specify either "celsius" or "fahrenheit" for the units.',
        ]);

        $location = $request->get('location');

        $forecast = $this->weather->getForecastFor($location);
        if (!$forecast) {
            return Response::error('Unable to fetch weather data. Please try again.');
        }

        // Get weather...
        return Response::text('Weather Summary: Sunny, 72°F');
    }

    /**
     * Handle the tool request.
     *
     * @return array<int, \Laravel\Mcp\Response>
     */
    public function handleMultipleResponses(Request $request): array
    {
        // ...

        return [
            Response::text('Weather Summary: Sunny, 72°F'),
            Response::text('**Detailed Forecast**\n- Morning: 65°F\n- Afternoon: 78°F\n- Evening: 70°F'),
        ];
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'location' => $schema->string()
                ->description('The location to get the weather for.')
                ->required(),

            'units' => $schema->string()
                ->enum(['celsius', 'fahrenheit'])
                ->description('The temperature units to use.')
                ->default('celsius'),
        ];
    }
}
