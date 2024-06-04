<?php declare(strict_types=1);

namespace App\Modules\Vacancy\Managers;

use Exception;
class VacancyDeleteException extends Exception
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

class VacancyDeleteManager
{
    use ValidateId;
    public function __construct(
        private VacancyDeleteUseCase $useCase,
        private JsonPresenter $presenter
    )
    {
    }

    /**
     * @throws VacancyDeleteException
     */
    public function __invoke(string $id): JsonResponse
    {
        $this->validateVacancyId($id);

        $response = $this->useCase->execute($id);

        return $this->presenter->present($response);
    }
}

class VacancyDeleteUseCase
{
    public function __construct(
        private VacancyDeleteRepository $repository
    )
    {
    }

    /**
     * @throws VacancyDeleteException
     */
    public function execute(string $id): array
    {
        try {
            return DB::transaction(function () use($id) {
                return $this->repository->make($id);
            });
        } catch (\Throwable $exception) {
            throw new VacancyDeleteException('Service unavailable', Response::HTTP_SERVICE_UNAVAILABLE, $exception);
        }
    }
}

class VacancyDeleteRepository
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
        $this->checkPermission(Auth::user(), 'delete', $this->vacancy);
        if ($this->vacancy->is_active) {
            $this->vacancy->is_active == false;
            $this->vacancy->save();
        } else {
            $this->vacancy->delete();
        }

        return [
            'message' => 'Удалено успешно!'
        ];
    }
}
