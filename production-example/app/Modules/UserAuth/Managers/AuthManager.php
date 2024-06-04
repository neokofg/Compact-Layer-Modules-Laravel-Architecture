<?php declare(strict_types=1);

namespace App\Modules\UserAuth\Managers;

use Exception;
class AuthException extends Exception
{
}

use App\Models\UserAuth;
use App\Modules\UserAuth\Requests\AuthDTO;
use App\Modules\UserAuth\Requests\AuthRequest;
use App\Presenters\JsonPresenter;
use App\Services\MailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
//use Junges\Kafka\Facades\Kafka;
//use Junges\Kafka\Message\Message;
use Symfony\Component\HttpFoundation\Response;
use function generateCode;

class AuthManager
{
    public function __construct(
        private JsonPresenter $presenter,
        private AuthUseCase $useCase
    )
    {
    }

    /**
     * @throws AuthException
     */
    public function __invoke(AuthRequest $request): JsonResponse
    {
        $DTO = $request->getValidated();

        $response = $this->useCase->execute($DTO);

        return $this->presenter->present($response);
    }
}

class AuthUseCase
{
    public function __construct(
        private AuthRepository $repository,
    )
    {
    }

    /**
     * @throws AuthException
     */
    public function execute(AuthDTO $DTO): array
    {
        try {
            return DB::transaction(function () use ($DTO) {
                return $this->repository->make($DTO);
            });
        } catch (\Throwable $exception) {
            throw new AuthException('Service unavailable', Response::HTTP_SERVICE_UNAVAILABLE, $exception);
        }
    }
}

class AuthRepository
{
    public function __construct(
        private UserAuth $userAuth,
        private MailService $mailService
    )
    {
    }

    public function make(AuthDTO $DTO): array
    {
        $this->userAuth->email = $DTO->email;
        $this->userAuth->code = generateCode();
        $this->userAuth->save();

//        $message = new Message(
//            body: [
//                'code' => $code,
//                'email' => $DTO->email
//            ]
//        );
//        Kafka::publish('172.17.0.1:9092')
//            ->onTopic('auth')
//            ->withMessage($message)
//            ->send();
        $this->mailService->userCodeSend($this->userAuth->code, $DTO->email);

        return [
            'email' => $DTO->email
        ];
    }
}
