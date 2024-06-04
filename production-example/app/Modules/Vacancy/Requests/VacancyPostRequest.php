<?php declare(strict_types=1);

namespace App\Modules\Vacancy\Requests;

use App\Helpers\DataTransferObject;
use Illuminate\Foundation\Http\FormRequest;

class VacancyPostRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:30'
        ];
    }

    public function getValidated(): VacancyPostRequestDTO
    {
        return new VacancyPostRequestDTO(
            ...$this->all()
        );
    }
}

readonly class VacancyPostRequestDTO extends DataTransferObject
{
    public function __construct(
        public string $name
    )
    {
    }
}
