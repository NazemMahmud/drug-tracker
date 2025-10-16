<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\UsersDrug;
use App\Repositories\Repository as BaseRepository;

class UsersDrugRepository extends BaseRepository
{
    public function setModel(): void
    {
        $this->model = new UsersDrug();
    }
}
