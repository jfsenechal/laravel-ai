<?php

namespace App\Console\Commands;

use App\Services\WeatherAiService;
use Illuminate\Console\Command;

class WeatherAiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:ai
                            {--location= : Specific location to query}
                            {--mode=auto : Mode: auto (with tools), simple, or structured}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Demonstrate OpenAI integration with MCP WeatherServer tools';

    /**
     * Execute the console command.
     */
    public function handle(WeatherAiService $service): void
    {
        $this->info('🌤️  Weather AI Demo - Using MCP WeatherServer with OpenAI');
        $this->newLine();

        $location = $this->option('location');
        $mode = $this->option('mode');

        match ($mode) {
            'simple' => $this->demonstrateSimpleMode($service, $location),
            'structured' => $this->demonstrateStructuredMode($service, $location),
            default => $this->demonstrateAutoMode($service, $location),
        };
    }

    /**
     * Demonstrate automatic mode with tool calling.
     */
    protected function demonstrateAutoMode(WeatherAiService $service, ?string $location): void
    {
        $this->warn('Mode: Auto (with OpenAI Function Calling)');
        $this->newLine();

        // If no location provided, ask user
        if (! $location) {
            $question = $this->ask('Ask me anything about weather (e.g., "What\'s the weather in Paris?")');
        } else {
            $question = "What's the weather in {$location}? Give me a detailed forecast.";
            $this->comment("Question: {$question}");
        }

        $this->info('🤖 Calling OpenAI with weather tools...');
        $this->newLine();

        $response = $service->getWeatherResponse($question);

        $this->info('Response:');
        $this->line($response);
    }

    /**
     * Demonstrate simple mode without tool calling.
     */
    protected function demonstrateSimpleMode(WeatherAiService $service, ?string $location): void
    {
        $this->warn('Mode: Simple (without tool calling)');
        $this->newLine();

        if (! $location) {
            $location = $this->ask('Enter a location', 'New York');
        }

        $this->info("🌍 Getting weather for: {$location}");
        $this->newLine();

        $response = $service->simpleWeatherQuery($location);

        $this->info('Response:');
        $this->line($response);
    }

    /**
     * Demonstrate structured mode with detailed output.
     */
    protected function demonstrateStructuredMode(WeatherAiService $service, ?string $location): void
    {
        $this->warn('Mode: Structured (with tool calls tracking)');
        $this->newLine();

        if (! $location) {
            $location = $this->ask('Enter a location', 'Tokyo');
        }

        $this->info("🌍 Getting structured weather for: {$location}");
        $this->newLine();

        $result = $service->getStructuredWeather($location);

        $this->info('Summary:');
        $this->line($result['summary']);
        $this->newLine();

        if (! empty($result['tool_calls'])) {
            $this->info('Tool Calls Made:');
            foreach ($result['tool_calls'] as $index => $call) {
                $this->line('  '.($index + 1).". {$call->name}");
                $this->line('     Arguments: '.json_encode($call->arguments()));
            }
        }
    }
}
