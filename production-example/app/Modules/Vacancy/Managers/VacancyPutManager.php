<?php declare(strict_types=1);

namespace App\Modules\Vacancy\Managers;

use Exception;
class VacancyPutException extends Exception
{
}

use App\Models\City;
use App\Models\Vacancy;
use App\Modules\Vacancy\Requests\VacancyPutRequest;
use App\Modules\Vacancy\Requests\VacancyPutRequestDTO;
use App\Presenters\JsonPresenter;
use App\Services\DadataService;
use App\Traits\CheckPermission;
use App\Traits\ValidateId;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class VacancyPutManager
{
    use ValidateId;
    public function __construct(
        private VacancyPutUseCase $useCase,
        private JsonPresenter $presenter
    )
    {
    }

    /**
     * @throws VacancyPutException
     */
    public function __invoke(VacancyPutRequest $request, string $id): JsonResponse
    {
        $this->validateVacancyId($id);

        $DTO = $request->getValidated();

        $response = $this->useCase->execute($DTO, $id);

        return $this->presenter->present($response);
    }
}

class VacancyPutUseCase
{
    public function __construct(
        private VacancyPutRepository $repository
    )
    {
    }

    /**
     * @throws VacancyPutException
     */
    public function execute(VacancyPutRequestDTO $DTO, string $id): array
    {
        try {
            return DB::transaction(function () use($DTO, $id) {
                return $this->repository->make($DTO, $id);
            });
        } catch (\Throwable $exception) {
            throw new VacancyPutException('Service unavailable', Response::HTTP_SERVICE_UNAVAILABLE, $exception);
        }
    }
}

class VacancyPutRepository
{
    use CheckPermission;
    public function __construct(
        private Vacancy $vacancy,
        private DadataService $dadataService
    )
    {
    }

    public function make(VacancyPutRequestDTO $DTO, string $id): array
    {
        $this->vacancy = Vacancy::find($id);
        $this->checkPermission(Auth::user(), 'update', $this->vacancy);
        $this->getCity($DTO);
        $this->vacancy->update($DTO->toArray(['fias_id']));

        return [
            'message' => 'Успешно обновлено!'
        ];
    }

    private function getCity(VacancyPutRequestDTO $DTO): void
    {
        if (isset($DTO->fias_id)) {
            $city = City::where('fias_id','=',$DTO->fias_id)->first();
            if(!$city) {
                $response = $this->dadataService->findById($DTO->fias_id);
                $city = new City();
                $city->name = $response[0]['data']['city'];
                $city->fias_id = $DTO->fias_id;
                $city->save();
            }
            $this->vacancy->city_id = $city->id;
            $this->vacancy->save();
        }
    }
}
