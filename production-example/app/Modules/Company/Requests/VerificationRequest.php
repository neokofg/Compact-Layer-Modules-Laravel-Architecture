<?php declare(strict_types=1);

namespace App\Modules\Company\Requests;

use App\Helpers\DataTransferObject;
use Illuminate\Foundation\Http\FormRequest;

class VerificationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'files' => 'required|array',
            'files.*' => 'required|file|mimes:jpeg,png,jpg,webp|max:10000',
        ];
    }

    public function getValidated(): VerificationDTO
    {
        return new VerificationDTO(
            ...$this->validated()
        );
    }
}

readonly class VerificationDTO extends DataTransferObject
{
    public function __construct(
        public array $files
    )
    {
    }
}
