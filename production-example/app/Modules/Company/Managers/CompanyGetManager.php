<?php declare(strict_types=1);

namespace App\Modules\Company\Managers;

use Exception;
class CompanyGetException extends Exception
{
}

use App\Models\Company;
use App\Presenters\JsonPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CompanyGetManager
{
    public function __construct(
        private CompanyGetUseCase $useCase,
        private JsonPresenter $presenter
    )
    {
    }

    /**
     * @throws CompanyGetException
     */
    public function __invoke(): JsonResponse
    {
        $response = $this->useCase->execute();

        return $this->presenter->present($response);
    }
}

class CompanyGetUseCase
{
    public function __construct(
        private CompanyGetRepository $repository
    )
    {
    }

    /**
     * @throws CompanyGetException
     */
    public function execute(): array
    {
        try {
            return DB::transaction(function () {
                return $this->repository->make();
            });
        } catch (\Throwable $exception) {
            throw new CompanyGetException('Service unavailable', Response::HTTP_SERVICE_UNAVAILABLE, $exception);
        }
    }
}

class CompanyGetRepository
{
    public function __construct(
        private Company $company
    )
    {
    }

    public function make(): array
    {
        $this->company = Company::find(Auth::user()->company_id);
        return [
            'company' => $this->company->makeHidden(['id','created_at','updated_at'])
        ];
    }
}
