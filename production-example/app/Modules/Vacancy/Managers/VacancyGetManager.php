<?php declare(strict_types=1);

namespace App\Modules\Vacancy\Managers;

use Exception;
class VacancyGetException extends Exception
{
}

use App\Models\Vacancy;
use App\Presenters\JsonPresenter;
use App\Traits\CheckPermission;
use App\Traits\ValidateId;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class VacancyGetManager
{
    use ValidateId;
    public function __construct(
        private VacancyGetUseCase $useCase,
        private JsonPresenter $presenter,
    )
    {
    }

    /**
     * @throws VacancyGetException
     */
    public function __invoke(string $id): JsonResponse
    {
        $this->validateVacancyId($id);

        $response = $this->useCase->execute($id);

        return $this->presenter->present($response);
    }
}

class VacancyGetUseCase
{
    public function __construct(
        private VacancyGetRepository $repository
    )
    {
    }

    /**
     * @throws VacancyGetException
     */
    public function execute(string $id): array
    {
        try {
            return DB::transaction(function () use($id) {
                return $this->repository->make($id);
            });
        } catch (\Throwable $exception) {
            throw new VacancyGetException('Service unavailable', Response::HTTP_SERVICE_UNAVAILABLE, $exception);
        }
    }
}

class VacancyGetRepository
{
    use CheckPermission;
    public function __construct(
        private Vacancy $vacancy
    )
    {
    }

    public function make(string $id): array
    {
        $this->vacancy = Vacancy::find($id);
        $this->checkPermission(Auth::user(), 'view', $this->vacancy);
        return [
            $this->vacancy->toArray()
        ];
    }
}
