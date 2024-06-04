<?php declare(strict_types=1);

namespace App\Modules\Vacancy\Managers;

use Exception;
class VacancyPostException extends Exception
{
}

use App\Models\Vacancy;
use App\Modules\Vacancy\Requests\VacancyPostRequest;
use App\Modules\Vacancy\Requests\VacancyPostRequestDTO;
use App\Presenters\JsonPresenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class VacancyPostManager
{
    public function __construct(
        private VacancyPostUseCase $useCase,
        private JsonPresenter $presenter
    )
    {
    }

    /**
     * @throws VacancyPostException
     */
    public function __invoke(VacancyPostRequest $request): JsonResponse
    {
        $DTO = $request->getValidated();

        $response = $this->useCase->execute($DTO);

        return $this->presenter->present($response);
    }
}

class VacancyPostUseCase
{
    public function __construct(
        private VacancyPostRepository $repository
    )
    {
    }

    /**
     * @throws VacancyPostException
     */
    public function execute(VacancyPostRequestDTO $DTO): array
    {
        try {
            return DB::transaction(function () use ($DTO) {
                return $this->repository->make($DTO);
            });
        } catch (\Throwable $exception) {
            throw new VacancyPostException('Service unavailable', Response::HTTP_SERVICE_UNAVAILABLE, $exception);
        }
    }
}

class VacancyPostRepository
{
    public function __construct(
        private Vacancy $vacancy
    )
    {
    }

    public function make(VacancyPostRequestDTO $DTO): array
    {
        $this->vacancy->name = $DTO->name;
        $this->vacancy->company_id = Auth::user()->company_id;
        $this->vacancy->save();

        return [
            'vacancy_id' => $this->vacancy->id
        ];
    }
}
