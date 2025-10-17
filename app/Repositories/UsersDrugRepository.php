<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\UsersDrug;
use App\Repositories\Repository as BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class UsersDrugRepository extends BaseRepository
{
    public function setModel(): void
    {
        $this->model = new UsersDrug();
    }

    public function getUserDrugsWithDetails(int $userId): Collection
    {
        return $this->model
            ->select('id', 'user_id', 'rxcui')
            ->with(['drug:rxcui,name,base_names,dose_form_group_names'])
            ->where('user_id', $userId)
            ->get();
    }
}
