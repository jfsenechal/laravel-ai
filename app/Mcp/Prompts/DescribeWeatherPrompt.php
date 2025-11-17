<?php

namespace App\Mcp\Prompts;

use App\Repository\BottinRepository;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Prompt;
use Laravel\Mcp\Server\Prompts\Argument;

class DescribeWeatherPrompt extends Prompt
{

    public function __construct(
        protected BottinRepository $weather,
    ) {
    }

    /**
     * The prompt's name.
     */
    protected string $name = 'weather-assistant';

    /**
     * The prompt's title.
     */
    protected string $title = 'Weather Assistant Prompt';

    /**
     * The prompt's description.
     */
    protected string $description = 'Generates a natural-language explanation of the weather for a given location.';

    /**
     * Handle the prompt request.
     */
    public function handle(Request $request): array
    {

        $validated = $request->validate([
            'tone' => ['required', 'string', 'max:50'],
        ], [
            'tone.*' => 'You must specify a tone for the weather description. Examples include "formal", "casual", or "humorous".',
        ]);

        $tone = $validated['tone'];
        $isAvailable = $this->weather->isServiceAvailable();
        $systemMessage = "You are a helpful weather assistant. Please provide a weather description in a {$tone} tone.";

        $userMessage = "What is the current weather like in New York City?";

        return [
            Response::text($systemMessage)->asAssistant(),
            Response::text($userMessage),
        ];

    }

    /**
     * Get the prompt's arguments.
     *
     * @return array<int, \Laravel\Mcp\Server\Prompts\Argument>
     */
    public function arguments(): array
    {
        return [
            new Argument(
                name: 'tone',
                description: 'The tone to use in the weather description (e.g., formal, casual, humorous).',
                required: true,
            ),
        ];
    }

}
