<?php

namespace App\Http\Controllers;

use App\Models\Document; // Asegúrate de importar el modelo Document
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Importa la fachada Storage

class DocumentController extends Controller
{
    /**
     * Muestra el archivo adjunto de un documento.
     *
     * @param  \App\Models\Document  $document  Laravel automáticamente inyectará el documento por su ID.
     * @return \Symfony\Component\HttpFoundation\StreamedResponse|\Illuminate\Http\RedirectResponse
     */
    public function viewFile(Document $document)
    {
        // Verifica si el documento tiene un archivo asociado
        if (!$document->file) {
            // Puedes redirigir a una página de error o mostrar un mensaje
            return redirect()->back()->with('error', 'El documento no tiene un archivo adjunto.');
        }

        $filePath = $document->file->path;
        $originalFileName = $document->file->original_name;
        $fileType = $document->file->file_type;

        // Asegúrate de que el archivo existe en el almacenamiento
        // Usamos el disco 'public' que es donde asumimos que estás guardando los archivos
        if (!Storage::disk('public')->exists($filePath)) {
            return redirect()->back()->with('error', 'El archivo no se encontró en el servidor.');
        }

        // Retorna el archivo para su visualización.
        // response()->file() es para mostrarlo en el navegador.
        // 'inline' sugiere al navegador que lo muestre, no que lo descargue.
        return response()->file(Storage::disk('public')->path($filePath), [
            'Content-Type' => $fileType,
            'Content-Disposition' => 'inline; filename="' . $originalFileName . '"',
        ]);
        // Si quisieras forzar la descarga, usarías:
        // return response()->download(Storage::disk('public')->path($filePath), $originalFileName, [
        //     'Content-Type' => $fileType,
        // ]);
    }
}
