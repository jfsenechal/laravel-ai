<?php

namespace App\Mcp\Resources;

use App\Repository\BottinRepository;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;

class WeatherGuidelinesResource extends Resource
{
    public function __construct(
        protected BottinRepository $weather,
    ) {
    }

    /**
     * The resource's name.
     */
    protected string $name = 'weather-api-docs';

    /**
     * The resource's title.
     */
    protected string $title = 'Weather API Documentation';

    /**
     * The resource's description.
     */
    protected string $description = 'Comprehensive guidelines for using the Weather API.';

    /**
     * The resource's URI.
     */
    protected string $uri = 'weather://resources/guidelines';

    /**
     * The resource's MIME type.
     * The default MIME type is text/plain
     */
    protected string $mimeType = 'application/pdf';

    /**
     * Handle the resource request.
     */
    public function handle(Request $request): Response
    {
        if (!$request->user()?->can('read-weather')) {
          //  return Response::error('Permission denied.');
        }
        $guidelines = $this->weather->guidelines();

        //return Response::blob(file_get_contents(storage_path('weather/radar.png'))); change mimeType to image/png
        return Response::text($guidelines);
    }
}
