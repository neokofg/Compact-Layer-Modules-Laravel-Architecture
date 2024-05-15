<?php

use App\Helpers\Presenters\JsonPresenter;
use Illuminate\Support\Facades\DB;
use Requests\UserAuthDTO;
use Requests\UserAuthRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserAuthController
{
    public function __construct(
        private UserAuthUseCase $useCase,
        private JsonPresenter $presenter
    )
    {
    }

    public function __invoke(UserAuthRequest $request): JsonResponse
    {
        $DTO = $request->getValidated();

        $response = $this->useCase->execute($DTO);

        return $this->presenter->present($response);
    }
}

class UserAuthUseCase
{
    public function __construct(
        private UserAuthRepository $repository
    )
    {
    }

    public function execute(UserAuthDTO $DTO): array
    {
        try {
            return DB::transaction(function () use ($DTO) {
                return $this->repository->make($DTO);
            });
        } catch (Throwable $exception) {
            throw new Exception('Service temporary unavailable', Response::HTTP_OK, $exception);
        }
    }
}

class UserAuthRepository
{
    public function __construct()
    {
    }

    public function make(UserAuthDTO $DTO): array
    {

    }
}
