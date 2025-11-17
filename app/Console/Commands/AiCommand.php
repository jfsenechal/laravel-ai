<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Enums\Provider;

class AiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ai';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {

        $response = Prism::text()
            ->using(Provider::OpenAI, 'gpt-5-mini')
            ->withPrompt('What is the profession of SENECHAL JEAN FRANCOIS FROM BELGIUM')
            ->withPrompt()
            ->asText();

        echo $response->text;
    }
}
