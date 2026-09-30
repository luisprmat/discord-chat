<?php

use App\Models\User;

test('precognitive requests return channel name validation errors', function () {
    $this->actingAs(User::factory()->create())
        ->withPrecognition()
        ->postJson(route('channels.store'), ['name' => 'X'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

test('precognitive requests with a valid name do not create the channel', function () {
    $this->actingAs(User::factory()->create())
        ->withPrecognition()
        ->postJson(route('channels.store'), ['name' => 'general'])
        ->assertNoContent();

    $this->assertDatabaseMissing('channels', ['name' => 'general']);
});
