<?php declare(strict_types=1);

namespace Requests;

use App\Helpers\DataTransferObject;
use Illuminate\Foundation\Http\FormRequest;

class UserAuthRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string'
        ];
    }

    public function getValidated(): UserAuthDTO
    {
        return new UserAuthDTO(
            ...$this->validated()
        );
    }
}

readonly class UserAuthDTO extends DataTransferObject
{
    public function __construct(
        public string $email,
        public string $password
    )
    {
    }
}
