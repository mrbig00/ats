<?php

use App\Actions\Housing\CreateApartmentAction;
use App\Data\Housing\ApartmentData;
use App\Models\Apartment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('create apartment action creates apartment', function () {
    $data = new ApartmentData(
        name: 'Test Apartment',
        address: '123 Main St',
        notes: 'Test notes',
    );

    $action = app(CreateApartmentAction::class);
    $apartment = $action->handle($data);

    expect($apartment)->toBeInstanceOf(Apartment::class)
        ->and($apartment->name)->toBe('Test Apartment')
        ->and($apartment->address)->toBe('123 Main St')
        ->and($apartment->notes)->toBe('Test notes')
        ->and(Apartment::count())->toBe(1);
});
