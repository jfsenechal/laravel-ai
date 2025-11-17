<?php

namespace App\Repository;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class BottinRepository
{
    /**
     * @return array<int, string>
     */
    public function getFiches(): array
    {
        try {
            $response = Http::timeout(30)
                ->get('https://api.marche.be/bottin/fichesandroid');

            $response->throw();

            return $response->json() ?? [];
        } catch (RequestException|ConnectionException $e) {
            report($e);

            return [];
        }
    }

    public function guidelines(): string
    {
        return "Weather Summary ";
    }

    public function isServiceAvailable(): bool
    {
        return true;
    }

    public function getForecastFor(string $location): string
    {

        return "Weather Summary: Sunny, 72°F in $location";
    }
}
