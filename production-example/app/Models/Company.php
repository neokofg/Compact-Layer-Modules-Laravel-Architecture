<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'companies';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function vacancies(): HasMany
    {
        return $this->hasMany(Vacancy::class, 'company_id', 'id');
    }
}
