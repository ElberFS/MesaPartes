<?php

namespace App\Services;

use App\Models\Office;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;

class OfficeService
{
    public function __construct(
        protected Office $model
    ) {}

    public function getAll(int $perPage = 10, string $search = ''): LengthAwarePaginator
    {
        return $this->model->query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('acronym', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    public function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->where('is_active', true)->get();
    }

    public function create(array $data): Office
    {
        return DB::transaction(function () use ($data) {
            return $this->model->create($data);
        });
    }

    public function update(Office $office, array $data): Office
    {
        return DB::transaction(function () use ($office, $data) {
            $office->update($data);
            return $office->refresh();
        });
    }

    public function delete(Office $office): bool
    {
        return DB::transaction(function () use ($office) {
            return $office->delete();
        });
    }
    
    public function toggleStatus(Office $office): Office
    {
        return DB::transaction(function () use ($office) {
            $office->update(['is_active' => !$office->is_active]);
            return $office;
        });
    }
}