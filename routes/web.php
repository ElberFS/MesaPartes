<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Livewire\Admin\Offices\CreateOffice;
use App\Livewire\Admin\Offices\ListOffices;
use App\Livewire\Admin\Offices\EditOffice;
use App\Livewire\Admin\Users\ListUsers;
use App\Livewire\Admin\Users\CreateUser;
use App\Livewire\Admin\Users\EditUser;
use App\Livewire\Other\Documents\CreateDocument;
use App\Livewire\Other\Documents\ListDocuments;
use App\Livewire\Other\Documents\EditDocument;
use App\Livewire\Other\Documents\ShowDocument;
use App\Http\Controllers\DocumentController;
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
        Route::get('/{documentId?}', ListResponses::class)->name('index');
    });

    // Rutas para Documentos
    Route::prefix('admin/documents')->name('documents.')->group(function () {
        Route::get('/', ListDocuments::class)->name('index');
    });
});

// Este grupo de rutas es para 'secretaria' y 'administrador'
Route::middleware(['role:secretaria|administrador'])->group(function () {
    Route::prefix('admin/documents')->name('documents.')->group(function () {
        Route::get('/create', CreateDocument::class)->name('create');
        Route::get('/{document}/edit', EditDocument::class)->name('edit');
        Route::get('/{document}/view-file', [DocumentController::class, 'viewFile'])->name('view-file');
        Route::get('/{document}', ShowDocument::class)->name('show');
    });

    Route::prefix('admin/responses')->name('responses.')->group(function () {
        Route::get('/create/{document?}', CreateResponse::class)->name('create');
        Route::get('/{response}/edit', EditResponse::class)->name('edit');
    });

});


require __DIR__.'/auth.php';
