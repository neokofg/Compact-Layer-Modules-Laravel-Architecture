<?php declare(strict_types=1);

namespace App\Modules\UserAuth\Requests;

use App\Helpers\DataTransferObject;
use Illuminate\Foundation\Http\FormRequest;

class ValidateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255',
            'code' => 'required|int|digits:4'
        ];
    }

    public function getValidated(): ValidateDTO
    {
        $data = $this->all();
        $data = array_merge($data, ['last_ip' => $this->ip()]);
        return new ValidateDTO(
            ...$data
        );
    }
}

readonly class ValidateDTO extends DataTransferObject
{
    public function __construct(
        public string $email,
        public int $code,
        public string $last_ip
    )
    {
    }
}
