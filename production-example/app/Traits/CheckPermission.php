<?php declare(strict_types=1);

namespace App\Traits;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;

trait CheckPermission
{
    public function checkPermission(User $user, string $permission, $model = null)
    {
        if($user->cannot($permission, $model)) {
            throw new AuthorizationException();
        }
    }
}
