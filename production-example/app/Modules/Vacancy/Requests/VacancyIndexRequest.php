<?php declare(strict_types=1);

namespace App\Modules\Vacancy\Requests;

use App\Helpers\DataTransferObject;
use Illuminate\Foundation\Http\FormRequest;

class VacancyIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first' => 'required|int|min:1',
            'page' => 'int|min:1',
            'date' => 'in:today,all',
        ];
    }

    public function getValidated(): VacancyIndexDTO
    {
        return new VacancyIndexDTO(
            intval($this->first),
            intval($this->page) ?? 1,
            $this->date ?? 'all',
        );
    }
}

readonly class VacancyIndexDTO extends DataTransferObject
{
    public function __construct(
        public int $first,
        public int $page,
        public string $date
    )
    {
    }
}
