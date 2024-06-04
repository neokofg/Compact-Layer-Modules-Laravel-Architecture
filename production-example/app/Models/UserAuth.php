<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAuth extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'user_auths';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];
}
