<?php declare(strict_types=1);

use App\Helpers\Presenters\JsonPresenter;
use Exceptions\InvalidCredentialsException;
use Illuminate\Support\Facades\Auth;
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

    /**
     * @throws Exception
     */
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

    /**
     * @throws Exception
     */
    public function execute(UserAuthDTO $DTO): array
    {
        try {
            return DB::transaction(function () use ($DTO) {
                return $this->repository->make($DTO);
            });
        } catch (InvalidCredentialsException $exception) {
            throw new Exception('Invalid credentials', Response::HTTP_FORBIDDEN, $exception);
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

    /**
     * @throws InvalidCredentialsException
     */
    public function make(UserAuthDTO $DTO): array
    {
        if(Auth::attempt([$DTO->toArray()])){
            return [
                'success' => true,
                'message' => 'User successfully logged in',
                'token' => Auth::user()->createToken('auth-token')->plainTextToken,
            ];
        } else {
            throw new InvalidCredentialsException();
        }
    }
}
