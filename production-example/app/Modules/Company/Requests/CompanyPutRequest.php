<?php declare(strict_types=1);

namespace App\Modules\Company\Requests;

use App\Helpers\DataTransferObject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class CompanyPutRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'string|max:30',
            'logo' => 'file|mimes:jpeg,jpg,png,webp',
            'description' => 'string|max:60',
            'website_url' => 'string|url|max:120'
        ];
    }

    public function getValidated(): CompanyPutRequestDTO
    {
        return new CompanyPutRequestDTO(
            ...$this->validated()
        );
    }
}

readonly class CompanyPutRequestDTO extends DataTransferObject
{
    public function __construct(
        public string|null $name = null,
        public UploadedFile|null $logo = null,
        public string|null $description = null,
        public string|null $website_url = null,
    )
    {
    }
}
