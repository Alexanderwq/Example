<?php

namespace App\Action\test\Repository;

use App\Model\MetroModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

readonly class MetroRepository
{
    public function getMetroList(): Collection|array
    {
        return MetroModel::query()
            ->get();
    }

    public function getMetroByDescriptor(string $descriptor): Model|null
    {
        return MetroModel::query()
            ->where('descriptor', '=', $descriptor)
            ->first();
    }
}
