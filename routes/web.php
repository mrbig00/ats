<?php

use App\Http\Controllers\CandidateDocumentController;
use App\Livewire\Candidates\CandidateList;
use App\Livewire\Candidates\CandidateShow;
use App\Livewire\Candidates\CreateCandidate;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('candidates', CandidateList::class)->name('candidates.index');
    Route::livewire('candidates/create', CreateCandidate::class)->name('candidates.create');
    Route::livewire('candidates/{candidate}', CandidateShow::class)->name('candidates.show');
    Route::get('candidates/{candidate}/documents/{document}/download', [CandidateDocumentController::class, 'download'])
        ->name('candidates.documents.download');
    Route::view('jobs', 'jobs.index')->name('jobs.index');
    Route::view('employees', 'employees.index')->name('employees.index');
    Route::view('housing', 'housing.index')->name('housing.index');
    Route::view('todo', 'todo.index')->name('todo.index');
    Route::view('meetings', 'meetings.index')->name('meetings.index');
});

require __DIR__.'/settings.php';
