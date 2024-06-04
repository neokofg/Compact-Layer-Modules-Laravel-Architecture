<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'cities';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    public function vacancies(): HasMany
    {
        return $this->hasMany(Vacancy::class, 'city_id', 'id');
    }
}
