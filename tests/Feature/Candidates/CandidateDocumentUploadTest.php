<?php

use App\Livewire\Candidates\CandidateShow;
use App\Models\Candidate;
use App\Models\User;
use Database\Seeders\PipelineStageSeeder;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(PipelineStageSeeder::class);
});

test('hr user can upload a candidate document', function () {
    $user = User::factory()->hr()->create();
    $candidate = Candidate::factory()->create();
    $this->actingAs($user);

    Livewire::test(CandidateShow::class, ['candidate' => $candidate])
        ->set('documentName', 'Employment contract')
        ->set('documentFile', UploadedFile::fake()->create('contract.pdf', 100, 'application/pdf'))
        ->call('uploadDocument')
        ->assertHasNoErrors();

    $media = $candidate->fresh()->getFirstMedia('documents');

    expect($media)->not->toBeNull()
        ->and($media->name)->toBe('Employment contract')
        ->and($media->collection_name)->toBe('documents');
});

test('viewer cannot upload a candidate document', function () {
    $user = User::factory()->viewer()->create();
    $candidate = Candidate::factory()->create();
    $this->actingAs($user);

    Livewire::test(CandidateShow::class, ['candidate' => $candidate])
        ->set('documentName', 'Employment contract')
        ->set('documentFile', UploadedFile::fake()->create('contract.pdf', 100, 'application/pdf'))
        ->call('uploadDocument')
        ->assertForbidden();

    expect($candidate->fresh()->getMedia('documents'))->toBeEmpty();
});

test('hr user can delete a candidate document', function () {
    $user = User::factory()->hr()->create();
    $candidate = Candidate::factory()->create();
    $this->actingAs($user);

    Livewire::test(CandidateShow::class, ['candidate' => $candidate])
        ->set('documentName', 'CV')
        ->set('documentFile', UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'))
        ->call('uploadDocument')
        ->assertHasNoErrors();

    $media = $candidate->fresh()->getFirstMedia('documents');

    Livewire::test(CandidateShow::class, ['candidate' => $candidate->fresh()])
        ->call('deleteDocument', $media->id)
        ->assertHasNoErrors();

    expect($candidate->fresh()->getMedia('documents'))->toBeEmpty();
});

test('authorized user can download an owned candidate document', function () {
    $user = User::factory()->hr()->create();
    $candidate = Candidate::factory()->create();
    $this->actingAs($user);

    Livewire::test(CandidateShow::class, ['candidate' => $candidate])
        ->set('documentName', 'Passport scan')
        ->set('documentFile', UploadedFile::fake()->create('passport.pdf', 100, 'application/pdf'))
        ->call('uploadDocument')
        ->assertHasNoErrors();

    $media = $candidate->fresh()->getFirstMedia('documents');

    $this->get(route('candidates.documents.download', [$candidate, $media]))
        ->assertOk();
});

test('download returns not found when media does not belong to candidate', function () {
    $user = User::factory()->hr()->create();
    $candidate = Candidate::factory()->create();
    $otherCandidate = Candidate::factory()->create();
    $this->actingAs($user);

    Livewire::test(CandidateShow::class, ['candidate' => $candidate])
        ->set('documentName', 'ID card')
        ->set('documentFile', UploadedFile::fake()->create('id.pdf', 100, 'application/pdf'))
        ->call('uploadDocument')
        ->assertHasNoErrors();

    $media = $candidate->fresh()->getFirstMedia('documents');

    $this->get(route('candidates.documents.download', [$otherCandidate, $media]))
        ->assertNotFound();
});
