<?php

namespace App\Helpers\Presenters;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class JsonPresenter
{
    public function present(array $response): JsonResponse
    {
        return response()->json($response, Response::HTTP_OK);
    }
}
