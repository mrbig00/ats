<?php

use App\Http\Controllers\CandidateDocumentController;
use App\Livewire\Candidates\CandidateList;
use App\Livewire\Candidates\CandidateShow;
use App\Livewire\Candidates\CreateCandidate;
use App\Livewire\Housing\ApartmentShow;
use App\Livewire\Housing\AssignOccupancy;
use App\Livewire\Housing\CreateApartment;
use App\Livewire\Housing\CreateRoom;
use App\Livewire\Housing\EditApartment;
use App\Livewire\Housing\EditRoom;
use App\Livewire\Housing\HousingList;
use App\Livewire\Meetings\CreateMeeting;
use App\Livewire\Meetings\EditMeeting;
use App\Livewire\Meetings\MeetingCalendar;
use App\Livewire\Meetings\MeetingList;
use App\Livewire\Meetings\MeetingShow;
use App\Livewire\Positions\CreatePosition;
use App\Livewire\Positions\EditPosition;
use App\Livewire\Positions\PositionList;
use App\Livewire\Positions\PositionShow;
use App\Livewire\Employees\EmployeeList;
use App\Livewire\Employees\EmployeeShow;
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
    Route::livewire('jobs', PositionList::class)->name('jobs.index');
    Route::livewire('jobs/create', CreatePosition::class)->name('jobs.create');
    Route::livewire('jobs/{position}', PositionShow::class)->name('jobs.show');
    Route::livewire('jobs/{position}/edit', EditPosition::class)->name('jobs.edit');
    Route::livewire('employees', EmployeeList::class)->name('employees.index');
    Route::livewire('employees/{employee}', EmployeeShow::class)->name('employees.show');
    Route::livewire('housing', HousingList::class)->name('housing.index');
    Route::livewire('housing/apartments/create', CreateApartment::class)->name('housing.apartments.create');
    Route::livewire('housing/apartments/{apartment}', ApartmentShow::class)->name('housing.apartments.show');
    Route::livewire('housing/apartments/{apartment}/edit', EditApartment::class)->name('housing.apartments.edit');
    Route::livewire('housing/apartments/{apartment}/rooms/create', CreateRoom::class)->name('housing.apartments.rooms.create');
    Route::livewire('housing/rooms/{room}/edit', EditRoom::class)->name('housing.rooms.edit');
    Route::livewire('housing/rooms/{room}/assign', AssignOccupancy::class)->name('housing.rooms.assign');
    Route::view('todo', 'todo.index')->name('todo.index');
    Route::livewire('meetings', MeetingList::class)->name('meetings.index');
    Route::livewire('meetings/calendar', MeetingCalendar::class)->name('meetings.calendar');
    Route::livewire('meetings/create', CreateMeeting::class)->name('meetings.create');
    Route::livewire('meetings/{calendarEvent}', MeetingShow::class)->name('meetings.show');
    Route::livewire('meetings/{calendarEvent}/edit', EditMeeting::class)->name('meetings.edit');
});

require __DIR__.'/settings.php';
