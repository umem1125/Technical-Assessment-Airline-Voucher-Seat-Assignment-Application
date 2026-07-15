<?php

use App\Models\Voucher;

test('it can check if voucher does not exists', function () {
    $response = $this->postJson('/api/check', [
        'flightNumber' => 'GA102',
        'date' => '2026-07-15'
    ]);

    $response->assertStatus(200)
        ->assertJson(['exists' => false]);
});

test('it can check if voucher exists', function () {
    // create a dummy data
    Voucher::create([
        'crew_name' => 'Umam',
        'crew_id' => '9999',
        'flight_number' => 'GA102',
        'flight_date' => '2026-07-15',
        'aircraft_type' => 'Airbus 20',
        'seat1' => '1A',
        'seat2' => '2A',
        'seat3' => '3A',
    ]);

    $response = $this->postJson('/api/check', [
        'flightNumber' => 'GA102',
        'date' => '2026-07-15',
    ]);

    $response->assertStatus(200)
        ->assertJson(['exists' => true]);
});

test('it can generate three unique and valid seats', function () {
    $response = $this->postJson('/api/generate', [
        'name' => 'Umam',
        'id' => '9999',
        'flightNumber' => 'ID102',
        'date' => '2026-07-15',
        'aircraft' => 'Airbus 320',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'data' => [
                'success',
                'seats',
            ]
        ]);

    $responseData = $response->json('data');

    expect($responseData['success'])->toBeTrue()
        ->and($responseData['seats'])->toHaveCount(3);

    $uniqueSeats = array_unique($responseData['seats']);
    expect(count($uniqueSeats))->toBe(3);
});

test('it prevents duplicate generation for same fligt and date', function () {
    $this->postJson('/api/generate', [
        'name' => 'Umam',
        'id' => '9999',
        'flightNumber' => 'GA102',
        'date' => '2026-07-15',
        'aircraft' => 'Airbus 320',
    ]);

    $response = $this->postJson('/api/generate', [
        'name' => 'John',
        'id' => '1111',
        'flightNumber' => 'GA102',
        'date' => '2026-07-15',
        'aircraft' => 'Boeing 737 Max',
    ]);

    $response->assertStatus(422);
});
