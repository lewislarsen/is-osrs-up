<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Services\RuneScapeService;

class ApiController
{
    private RuneScapeService $runescapeService;

    public function __construct(RuneScapeService $runescapeService)
    {
        $this->runescapeService = $runescapeService;
    }

    public function getStatus(Request $request, Response $response): Response
    {
        $status = $this->runescapeService->getLatestStatus();

        $response->getBody()->write(json_encode($status, JSON_THROW_ON_ERROR));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }

    public function getHistory(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $limit = isset($params['limit']) ? (int) $params['limit'] : 10;

        $history = $this->runescapeService->getStatusHistory($limit);

        $response->getBody()->write(json_encode([
            'count' => count($history),
            'data' => $history
        ], JSON_THROW_ON_ERROR));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}