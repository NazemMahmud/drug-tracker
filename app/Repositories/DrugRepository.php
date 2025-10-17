<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Drug;
use App\Repositories\Repository as BaseRepository;

class DrugRepository extends BaseRepository
{
    public function setModel(): void
    {
        $this->model = new Drug();
    }
}
