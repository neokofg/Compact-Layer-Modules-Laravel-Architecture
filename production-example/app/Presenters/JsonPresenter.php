<?php declare(strict_types=1);

namespace App\Presenters;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonPresenter
{
    const HEADER_SERVER_TIME = 'X-Server-Time';
    public function present(array $response): JsonResponse
    {
        return response()
            ->json($response)
            ->header(self::HEADER_SERVER_TIME, now()->format('Y-m-d H:i:s'))
            ->setStatusCode(Response::HTTP_OK);
    }
}
