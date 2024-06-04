<?php declare(strict_types=1);

namespace App\Modules\Company\Managers;

use Exception;
class CompanyPutException extends Exception
{
}

use App\Models\Company;
use App\Modules\Company\Requests\CompanyPutRequest;
use App\Modules\Company\Requests\CompanyPutRequestDTO;
use App\Presenters\JsonPresenter;
use App\Services\StorageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CompanyPutManager
{
    public function __construct(
        private CompanyPutUseCase $useCase,
        private JsonPresenter $presenter
    )
    {
    }

    /**
     * @throws CompanyPutException
     */
    public function __invoke(CompanyPutRequest $request): JsonResponse
    {
        $DTO = $request->getValidated();

        $response = $this->useCase->execute($DTO);

        return $this->presenter->present($response);
    }
}

class CompanyPutUseCase
{
    public function __construct(
        private CompanyPutRepository $repository
    )
    {
    }

    /**
     * @throws CompanyPutException
     */
    public function execute(CompanyPutRequestDTO $DTO): array
    {
        try {
            return DB::transaction(function () use ($DTO) {
                return $this->repository->make($DTO);
            });
        } catch (\Throwable $exception) {
            throw new CompanyPutException('Service unavailable', Response::HTTP_SERVICE_UNAVAILABLE, $exception);
        }
    }
}

class CompanyPutRepository
{
    public function __construct(
        private Company $company,
        private StorageService $service
    )
    {
    }

    public function make(CompanyPutRequestDTO $DTO): array
    {
        $this->company = Company::find(Auth::user()->company_id);
        $this->attachImage($DTO);
        $this->company->update($DTO->toArray(['logo']));
        return [
            'message' => 'Успешно обновлено!'
        ];
    }

    private function attachImage(CompanyPutRequestDTO $DTO): void
    {
        if(isset($DTO->logo)) {
            $url = $this->service->putOne($DTO->logo, 'logos/');
            $this->company->avatar_url = $url;
            $this->company->save();
        }
    }
}
