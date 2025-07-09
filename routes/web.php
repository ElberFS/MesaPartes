<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Admin\Offices\CreateOffice;
use App\Livewire\Admin\Offices\ListOffices;
use App\Livewire\Admin\Offices\EditOffice;
use App\Livewire\Admin\Users\ListUsers;
use App\Livewire\Admin\Users\CreateUser;
use App\Livewire\Admin\Users\EditUser;

// Componentes de Documentos
use App\Livewire\Other\Documents\CreateDocument;
use App\Livewire\Other\Documents\ListDocuments;
use App\Livewire\Other\Documents\EditDocument;
use App\Livewire\Other\Documents\ShowDocument; // Importa ShowDocument para su uso en rutas de documentos

use App\Http\Controllers\DocumentController;

// Componentes de Respuestas (asegúrate que el namespace sea 'Responses' en plural)
use App\Livewire\Other\Responses\CreateResponse;
use App\Livewire\Other\Responses\EditResponse;
use App\Livewire\Other\Responses\ListResponses;


Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::middleware(['role:administrador'])->group(function () {
    Route::prefix('admin/offices')->name('offices.')->group(function () {
        Route::get('/', ListOffices::class)->name('index');
        Route::get('/create', CreateOffice::class)->name('create');
        Route::get('/{office}/edit', EditOffice::class)->name('edit');
    });

    Route::prefix('admin/users')->name('users.')->group(function () {
        Route::get('/', ListUsers::class)->name('index');
        Route::get('/create', CreateUser::class)->name('create');
        Route::get('/{user}/edit', EditUser::class)->name('edit');
    });

    // Rutas para Respuestas
    Route::prefix('admin/responses')->name('responses.')->group(function () {
        // ¡CAMBIO AQUÍ! Acepta un documentId opcional como parámetro de ruta
        Route::get('/{documentId?}', ListResponses::class)->name('index');
        Route::get('/create/{document?}', CreateResponse::class)->name('create'); // document es opcional
        Route::get('/{response}/edit', EditResponse::class)->name('edit');
    });
});

// Este grupo de rutas es para 'secretaria' y 'administrador'
Route::middleware(['role:secretaria|administrador'])->group(function () {
    Route::prefix('admin/documents')->name('documents.')->group(function () {
        Route::get('/', ListDocuments::class)->name('index');
        Route::get('/create', CreateDocument::class)->name('create');
        Route::get('/{document}/edit', EditDocument::class)->name('edit');
        Route::get('/{document}/view-file', [DocumentController::class, 'viewFile'])->name('view-file');
        Route::get('/{document}', ShowDocument::class)->name('show');
    });
});


require __DIR__.'/auth.php';
