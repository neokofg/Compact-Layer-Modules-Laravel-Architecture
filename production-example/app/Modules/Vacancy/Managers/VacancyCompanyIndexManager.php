<?php declare(strict_types=1);

namespace App\Modules\Vacancy\Managers;

use Exception;
class VacancyCompanyIndexException extends Exception
{
}

use App\Models\Vacancy;
use App\Presenters\JsonPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class VacancyCompanyIndexManager
{
    public function __construct(
        private VacancyCompanyIndexUseCase $useCase,
        private JsonPresenter $presenter
    )
    {
    }

    /**
     * @throws VacancyCompanyIndexException
     */
    public function __invoke(): JsonResponse
    {
        $response = $this->useCase->execute();

        return $this->presenter->present($response);
    }
}

class VacancyCompanyIndexUseCase
{
    public function __construct(
        private VacancyCompanyIndexRepository $repository
    )
    {
    }

    /**
     * @throws VacancyCompanyIndexException
     */
    public function execute(): array
    {
        try {
            return DB::transaction(function () {
                return $this->repository->make();
            });
        } catch (\Throwable $exception) {
            throw new VacancyCompanyIndexException('Service unavailable', Response::HTTP_SERVICE_UNAVAILABLE, $exception);
        }
    }
}

class VacancyCompanyIndexRepository
{
    public function make(): array
    {
        $vacancies = Vacancy::where('company_id','=',Auth::user()->company_id)->with(['city'])->get();

        return [
            $vacancies->toArray()
        ];
    }
}
