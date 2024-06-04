<?php declare(strict_types = 1);

namespace App\Modules\Vacancy\Managers;

use App\Models\Vacancy;
use App\Presenters\JsonPresenter;
use App\Traits\ValidateId;
use Exception;
class VacancyShowException extends Exception
{
}

use Illuminate\Http\JsonResponse;
use \Throwable;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class VacancyShowManager
{
    use ValidateId;
    public function __construct(
        private VacancyShowUseCase $useCase,
        private JsonPresenter $presenter
    )
    {
    }
    /**
     * @throws VacancyShowException
     */
    public function __invoke(string $id): JsonResponse
    {
        $this->validateVacancyId($id);

        $response = $this->useCase->execute($id);

        return $this->presenter->present($response);
    }
}

class VacancyShowUseCase
{
    public function __construct(
        private VacancyShowRepository $repository
    )
    {
    }

    /**
     * @throws VacancyShowException
     */
    public function execute(string $id): array
    {
        try {
            return DB::transaction(function () use ($id) {
                return $this->repository->make($id);
            });
        } catch (Throwable $exception) {
            throw new VacancyShowException('Service unavailable', Response::HTTP_SERVICE_UNAVAILABLE, $exception);
        }
    }
}

class VacancyShowRepository
{
    public function make(string $id): array
    {
        $vacancy = Vacancy::find($id);

        return [
            'id' => $vacancy->id,
            'created_at' => $vacancy->created_at,
            'name' => $vacancy->name,
            'salary' => $vacancy->salary,
            'is_some' => $vacancy->is_some,
            'description' => $vacancy->description,
            'contact' => $vacancy->contact,
            'city' => [
                'id' => $vacancy->city->id,
                'name' => $vacancy->city->name,
            ],
            'company' => [
                'id' => $vacancy->company->id,
                'name' => $vacancy->company->name,
                'avatar_url' => $vacancy->company->avatar_url,
                'description' => $vacancy->company->description,
                'is_verified' => false
            ]
        ];
    }
}
