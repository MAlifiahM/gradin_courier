<?php

namespace Tests\Feature;

use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourierApiTest extends TestCase
{
    use RefreshDatabase;

    private function courierData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Budiono Hadi Agung',
            'phone' => '081234567890',
            'email' => 'budiono@example.com',
            'address' => 'Balikpapan',
            'level' => 3,
            'is_active' => true,
        ], $overrides);
    }

    public function test_can_get_courier_list(): void
    {
        Courier::factory()->count(3)->create();

        $response = $this->getJson('/api/couriers');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_can_search_courier_by_name(): void
    {
        Courier::factory()->create([
            'name' => 'Budiono Hadi Agung',
        ]);

        Courier::factory()->create([
            'name' => 'Andi Saputra',
        ]);

        $response = $this->getJson(
            '/api/couriers?search=budi agung'
        );

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath(
                'data.0.name',
                'Budiono Hadi Agung'
            );
    }

    public function test_can_filter_couriers_by_level(): void
    {
        Courier::factory()->create(['level' => 2]);
        Courier::factory()->create(['level' => 3]);
        Courier::factory()->create(['level' => 4]);

        $response = $this->getJson('/api/couriers?level=2,3');

        $response->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_can_paginate_couriers(): void
    {
        Courier::factory()->count(15)->create();

        $response = $this->getJson(
            '/api/couriers?page=1&per_page=10'
        );

        $response->assertOk()
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('current_page', 1)
            ->assertJsonPath('per_page', 10);
    }

    public function test_can_show_courier_detail(): void
    {
        $courier = Courier::factory()->create();

        $response = $this->getJson(
            "/api/couriers/{$courier->id}"
        );

        $response->assertOk()
            ->assertJsonPath('data.id', $courier->id)
            ->assertJsonPath('data.name', $courier->name);
    }

    public function test_returns_404_when_courier_not_found(): void
    {
        $response = $this->getJson('/api/couriers/99999');

        $response->assertNotFound();
    }

    public function test_can_create_courier(): void
    {
        $data = $this->courierData();

        $response = $this->postJson('/api/couriers', $data);

        $response->assertCreated()
            ->assertJsonPath('data.name', $data['name']);

        $this->assertDatabaseHas('couriers', [
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);
    }

    public function test_cannot_create_courier_with_invalid_data(): void
    {
        $response = $this->postJson('/api/couriers', [
            'name' => '',
            'email' => 'invalid-email',
            'level' => 10,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors([
                'name',
                'phone',
                'email',
                'address',
                'level',
                'is_active',
            ]);
    }

    public function test_cannot_create_courier_with_duplicate_email(): void
    {
        $courier = Courier::factory()->create();

        $response = $this->postJson(
            '/api/couriers',
            $this->courierData([
                'email' => $courier->email,
            ])
        );

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_can_update_courier_using_put(): void
    {
        $courier = Courier::factory()->create();

        $data = $this->courierData([
            'name' => 'Updated Courier',
            'phone' => '081234567891',
            'email' => 'updated@example.com',
        ]);

        $response = $this->putJson(
            "/api/couriers/{$courier->id}",
            $data
        );

        $response->assertOk()
            ->assertJsonPath('data.name', 'Updated Courier');

        $this->assertDatabaseHas('couriers', [
            'id' => $courier->id,
            'name' => 'Updated Courier',
        ]);
    }

    public function test_can_partially_update_courier_using_patch(): void
    {
        $courier = Courier::factory()->create();

        $response = $this->patchJson(
            "/api/couriers/{$courier->id}",
            [
                'phone' => '081234567899',
            ]
        );

        $response->assertOk()
            ->assertJsonPath('data.phone', '081234567899');

        $this->assertDatabaseHas('couriers', [
            'id' => $courier->id,
            'phone' => '081234567899',
            'name' => $courier->name,
        ]);
    }

    public function test_cannot_patch_courier_with_invalid_email(): void
    {
        $courier = Courier::factory()->create();

        $response = $this->patchJson(
            "/api/couriers/{$courier->id}",
            [
                'email' => 'invalid-email',
            ]
        );

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['email']);
    }

    public function test_can_delete_courier(): void
    {
        $courier = Courier::factory()->create();

        $response = $this->deleteJson(
            "/api/couriers/{$courier->id}"
        );

        $response->assertOk();

        $this->assertDatabaseMissing('couriers', [
            'id' => $courier->id,
        ]);
    }

    public function test_cannot_delete_nonexistent_courier(): void
    {
        $response = $this->deleteJson('/api/couriers/99999');

        $response->assertNotFound();
    }
}
