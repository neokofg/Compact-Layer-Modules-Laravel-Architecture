<?php declare(strict_types=1);

namespace App\Modules\UserAuth\Requests;

use App\Helpers\DataTransferObject;
use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255'
        ];
    }

    public function getValidated(): AuthDTO
    {
        return new AuthDTO(
            ...$this->all()
        );
    }
}

readonly class AuthDTO extends DataTransferObject
{
    public function __construct(
        public string $email
    )
    {
    }
}
