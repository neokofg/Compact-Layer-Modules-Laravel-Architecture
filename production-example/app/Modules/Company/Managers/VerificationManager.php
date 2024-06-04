<?php declare(strict_types = 1);

namespace App\Modules\Company\Managers;

use App\Models\CompanyVerification;
use App\Modules\Company\Requests\VerificationDTO;
use App\Modules\Company\Requests\VerificationRequest;
use App\Presenters\JsonPresenter;
use App\Services\StorageService;
use Exception;
class VerificationException extends Exception
{
}

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use \Throwable;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class VerificationManager
{
    public function __construct(
        private VerificationUseCase $useCase,
        private JsonPresenter $presenter
    )
    {
    }
    /**
     * @throws VerificationException
     */
    public function __invoke(VerificationRequest $request): JsonResponse
    {
        $DTO = $request->getValidated();

        $response = $this->useCase->execute($DTO);

        return $this->presenter->present($response);
    }
}

class VerificationUseCase
{
    public function __construct(
        private VerificationRepository $repository
    )
    {
    }

    /**
     * @throws VerificationException
     */
    public function execute(VerificationDTO $DTO): array
    {
        try {
            return DB::transaction(function () use($DTO) {
                return $this->repository->make($DTO);
            });
        } catch (Throwable $exception) {
            throw new VerificationException('Service unavailable', Response::HTTP_SERVICE_UNAVAILABLE, $exception);
        }
    }
}

class VerificationRepository
{
    public function __construct(
        private StorageService $storageService
    )
    {
    }

    public function make(VerificationDTO $DTO): array
    {
        foreach ($DTO->files as $file) {
            $url = $this->storageService->putOne($file, 'docs/');
            $company_verif = new CompanyVerification();
            $company_verif->url = $url;
            $company_verif->company_id = Auth::user()->company_id;
            $company_verif->save();
        }
        return [
            'success' => 'true'
        ];
    }
}
