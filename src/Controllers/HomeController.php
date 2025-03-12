<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\RuneScapeService;
use Illuminate\Support\Facades\Response as ResponseFacade;

class HomeController
{
    private RuneScapeService $runeScapeService;
    private array $settings;

    public function __construct(RuneScapeService $runeScapeService, array $settings)
    {
        $this->runeScapeService = $runeScapeService;
        $this->settings = $settings;
    }

    public function index(Request $request, Response $response): Response
    {
        $status = $this->runeScapeService->getLatestStatus();
        $downtimeHistory = $this->runeScapeService->getDowntimeHistory();

        $data = [
            'status' => $status,
            'history' => $downtimeHistory,
            'refresh_interval' => $this->settings['check_interval'],
            'source_url' => $this->settings['source_url']
        ];

        $content = $this->renderView('home', $data);
        $response->getBody()->write($content);

        return $response;
    }

    private function renderView(string $view, array $data = []): string
    {
        $layout = $this->getLayoutContent();
        $content = $this->getViewContent($view, $data);

        return str_replace('{{content}}', $content, $layout);
    }

    private function getLayoutContent(): string
    {
        ob_start();
        include __DIR__ . '/../views/layouts/main.php';
        return ob_get_clean();
    }

    private function getViewContent(string $view, array $data = []): string
    {
        foreach ($data as $key => $value) {
            $$key = $value;
        }

        ob_start();
        include __DIR__ . "/../views/{$view}.php";
        return ob_get_clean();
    }
}