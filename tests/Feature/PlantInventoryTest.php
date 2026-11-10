<?php

namespace Tests\Feature;

use App\Models\Plant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlantInventoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_administrator_pages(): void
    {
        $this->get(route('plants.index'))->assertRedirect(route('login'));
        $this->get(route('reports.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_log_in_by_email_or_username_and_log_out(): void
    {
        $user = User::factory()->create(['username' => 'garden-admin']);

        $this->post(route('login.store'), [
            'login' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('plants.index'));

        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->post(route('login.store'), [
            'login' => 'garden-admin',
            'password' => 'password',
        ])->assertRedirect(route('plants.index'));
    }

    public function test_plant_crud_validates_price_and_records_creator(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $attributes = [
            'name' => 'Aloe Vera',
            'category' => 'Succulent',
            'unit_price' => '12.50',
            'description' => 'A hardy indoor plant.',
            'stock_status' => 'in_stock',
        ];

        $this->post(route('plants.store'), $attributes)->assertRedirect(route('plants.index'));
        $plant = Plant::query()->firstOrFail();
        $this->assertSame($user->id, $plant->user_id);
        $this->assertSame('12.50', $plant->unit_price);

        $this->put(route('plants.update', $plant), array_merge($attributes, [
            'name' => 'Updated Aloe',
            'unit_price' => '-1',
        ]))->assertSessionHasErrors('unit_price');

        $this->put(route('plants.update', $plant), array_merge($attributes, [
            'name' => 'Updated Aloe',
            'unit_price' => '15',
        ]))->assertRedirect(route('plants.show', $plant));
        $this->assertDatabaseHas('plants', ['id' => $plant->id, 'name' => 'Updated Aloe']);

        $this->delete(route('plants.destroy', $plant))->assertRedirect(route('plants.index'));
        $this->assertDatabaseMissing('plants', ['id' => $plant->id]);
    }

    public function test_report_filters_are_applied_to_page_and_exports(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Plant::factory()->create([
            'user_id' => $user->id,
            'name' => 'Rosemary',
            'category' => 'Herb',
            'stock_status' => 'in_stock',
        ]);
        Plant::factory()->create([
            'user_id' => $user->id,
            'name' => 'Lavender',
            'category' => 'Flowering',
            'stock_status' => 'out_of_stock',
        ]);

        $filters = ['category' => 'Herb', 'stock_status' => 'in_stock', 'report_type' => 'by_category'];
        $this->get(route('reports.index', $filters))
            ->assertOk()
            ->assertSee('Rosemary')
            ->assertDontSee('Lavender');

        $excelResponse = $this->get(route('reports.export.excel', $filters));
        $excelResponse
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Rosemary', $excelResponse->streamedContent());
        $this->assertStringNotContainsString('Lavender', $excelResponse->streamedContent());

        $this->get(route('reports.export.pdf', $filters))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertSee('%PDF-1.4', false)
            ->assertSee('Rosemary', false)
            ->assertDontSee('Lavender', false);
    }
}
