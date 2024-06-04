<?php declare(strict_types=1);

namespace App\Modules\UserAuth\Managers;

use Exception;
class ValidateException extends Exception
{
}
class InvalidCodeException extends Exception
{
}

use App\Models\User;
use App\Models\UserAuth;
use App\Modules\UserAuth\Requests\ValidateDTO;
use App\Modules\UserAuth\Requests\ValidateRequest;
use App\Presenters\JsonPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ValidateManager
{
    public function __construct(
        private ValidateUseCase $useCase,
        private JsonPresenter $presenter
    )
    {
    }

    /**
     * @throws ValidateException
     */
    public function __invoke(ValidateRequest $request): JsonResponse
    {
        $DTO = $request->getValidated();

        $response = $this->useCase->execute($DTO);

        return $this->presenter->present($response);
    }
}

class ValidateUseCase
{
    public function __construct(
        private ValidateRepository $repository
    )
    {
    }

    /**
     * @throws ValidateException
     */
    public function execute(ValidateDTO $DTO): array
    {
        try {
            return DB::transaction(function () use ($DTO) {
                return $this->repository->make($DTO);
            });
        } catch (InvalidCodeException $exception) {
            throw new ValidateException('Неверный код', Response::HTTP_FORBIDDEN, $exception);
        } catch (\Throwable $exception) {
            throw new ValidateException('Service unavailable', Response::HTTP_SERVICE_UNAVAILABLE, $exception);
        }
    }
}

class ValidateRepository
{
    public function __construct(
        private UserAuth $userAuth,
        private string|null $token = null,
        private bool|null $is_filled = null,
    )
    {
    }

    /**
     * @throws InvalidCodeException
     */
    public function make(ValidateDTO $DTO): array
    {
        $this->userAuth = UserAuth::where('email','=',$DTO->email)->first();
        if ($this->userAuth->code != $DTO->code)
            throw new InvalidCodeException();

        if ( ! $this->checkUser($DTO->last_ip) )
            $this->createUser($DTO->email, $DTO->last_ip);

        $this->userAuth->delete();

        return [
            'token' => $this->token,
            'is_filled' => $this->is_filled
        ];
    }

    private function createUser(string $email, string $ip): void
    {
        $user = new User();
        $user->email = $email;
        $user->last_ip = $ip;
        $user->save();

        $this->token = $user->createToken('auth_token')->plainTextToken;
        $this->is_filled = false;
    }

    private function checkUser(string $ip): bool
    {
        if ( $user = User::where('email','=',$this->userAuth->email)->first() ) {
            $user->last_ip = $ip;
            $user->save();

            $this->token = $user->createToken('auth_token')->plainTextToken;
            $this->is_filled = isset($user->company->name);
            return true;
        }
        return false;
    }
}
