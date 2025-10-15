<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Repository as BaseRepository;

use Illuminate\Support\Facades\Hash;

class AuthRepository extends BaseRepository
{
    public function setModel(): void
    {
        $this->model = new User();
    }
}
