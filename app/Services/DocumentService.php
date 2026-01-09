<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Office;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class DocumentService
{
    public function __construct(
        protected Document $model,
        protected CodeGeneratorService $codeGenerator
    ) {}

    public function getAll(
        int $perPage = 10,
        string $search = ''
    ): LengthAwarePaginator {
        return $this->model
            ->with(['office', 'expediente', 'lastDerivation'])
            ->when($search, function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate($perPage);
    }

    public function create(
        array $data,
        UploadedFile $file,
        Office $office
    ): Document {
        return DB::transaction(function () use ($data, $file, $office) {

            $code = $this->codeGenerator->generate(
                prefix: 'D',
                tableName: 'documents',
                office: $office
            );

            $path = $file->store('documents', 'public');

            return $this->model->create([
                'code'          => $code,
                'subject'       => $data['subject'],
                'description'   => $data['description'] ?? null,
                'file_path'     => $path,
                'office_id'     => $office->id,
                'expediente_id' => $data['expediente_id'] ?? null,
            ]);
        });
    }

    public function update(
        Document $document,
        array $data,
        ?UploadedFile $file = null
    ): Document {
        return DB::transaction(function () use ($document, $data, $file) {

            if ($file) {
                if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                    Storage::disk('public')->delete($document->file_path);
                }

                $data['file_path'] = $file->store('documents', 'public');
            }

            $document->update([
                'subject'       => $data['subject'],
                'description'   => $data['description'] ?? null,
                'expediente_id' => $data['expediente_id'] ?? null,
                'file_path'     => $data['file_path'] ?? $document->file_path,
            ]);

            return $document->refresh();
        });
    }

    public function delete(Document $document): bool
    {
        return DB::transaction(function () use ($document) {

            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }

            return $document->delete();
        });
    }


    public function previewCode(Office $office): string
    {
        return $this->codeGenerator->generate(
            prefix: 'D',
            tableName: 'documents',
            office: $office
        );
    }
}
