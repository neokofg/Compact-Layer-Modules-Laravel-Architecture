<?php declare(strict_types = 1);

namespace App\Modules\Vacancy\Managers;

use App\Models\Vacancy;
use App\Modules\Vacancy\Requests\VacancyIndexDTO;
use App\Modules\Vacancy\Requests\VacancyIndexRequest;
use App\Presenters\JsonPresenter;
use Exception;
class VacancyIndexException extends Exception
{
}

use Illuminate\Http\JsonResponse;
use \Throwable;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class VacancyIndexManager
{
    public function __construct(
        private VacancyIndexUseCase $useCase,
        private JsonPresenter $presenter
    )
    {
    }
    /**
     * @throws VacancyIndexException
     */
    public function __invoke(VacancyIndexRequest $request): JsonResponse
    {
        $DTO = $request->getValidated();

        $response = $this->useCase->execute($DTO);

        return $this->presenter->present($response);
    }
}

class VacancyIndexUseCase
{
    public function __construct(
        private VacancyIndexRepository $repository
    )
    {
    }

    /**
     * @throws VacancyIndexException
     */
    public function execute(VacancyIndexDTO $DTO): array
    {
        try {
            return DB::transaction(function () use($DTO){
                return $this->repository->make($DTO);
            });
        } catch (Throwable $exception) {
            throw new VacancyIndexException('Service unavailable', Response::HTTP_SERVICE_UNAVAILABLE, $exception);
        }
    }
}

class VacancyIndexRepository
{
    public function __construct(
        private mixed $vacancies = null
    )
    {
    }

    public function make(VacancyIndexDTO $DTO): array
    {
        $this->vacancies = Vacancy::where('is_active','=',true);
        $this->date($DTO->date);

        $this->vacancies = $this->vacancies->paginate($DTO->first, page: $DTO->page);
        $this->vacancies = $this->vacancies->appends([
            'first' => $DTO->first
        ]);

        return [$this->vacancies];
    }

    private function date(string $date): void
    {
        switch ($date) {
            case 'all':
                return;
            case 'today':
                $this->vacancies = $this->vacancies->where('created_at', '=', now()->format('Y-m-d'));
                break;
        }
    }
}
