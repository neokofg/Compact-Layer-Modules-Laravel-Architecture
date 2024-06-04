<?php declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Facades\Validator;

trait ValidateId
{
    public function validateVacancyId(string $id)
    {
        Validator::make(['id' => $id], [
            'id' => 'required|ulid|exists:vacancies,id',
        ])->validate();
    }
}
