<?php declare(strict_types=1);

namespace App\Modules\Vacancy\Requests;

use App\Helpers\DataTransferObject;
use Illuminate\Foundation\Http\FormRequest;

class VacancyPutRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'string|max:30',
            'fias_id' => 'uuid|string',
            'salary' => 'int',
            'is_some' => 'bool',
            'description' => 'string|max:600',
            'contact' => 'string|max:255'
        ];
    }

    public function getValidated(): VacancyPutRequestDTO
    {
        return new VacancyPutRequestDTO(
            ...$this->all()
        );
    }
}

readonly class VacancyPutRequestDTO extends DataTransferObject
{
    public function __construct(
        public string|null  $name = null,
        public string|null  $fias_id = null,
        public int|null     $salary = null,
        public bool|null    $is_some = null,
        public string|null  $description = null,
        public string|null  $contact = null,
    )
    {
    }
}
