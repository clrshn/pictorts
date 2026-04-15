<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\Office;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TravelOrderFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);
    }

    private function createOffice(string $code, string $name): Office
    {
        return Office::create([
            'code' => $code,
            'name' => $name,
        ]);
    }

    public function test_travel_order_index_page_can_be_opened(): void
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->get(route('documents.index', ['type' => 'TO']));

        $response->assertOk();
        $response->assertSee('Travel Orders');
        $response->assertSee('Add Travel Order');
    }

    public function test_travel_order_can_be_created_without_losing_internal_outgoing_rules(): void
    {
        $user = $this->createUser();
        $office = $this->createOffice('PICTO', 'Provincial Information and Communications Technology Office');

        $response = $this->actingAs($user)->post(route('documents.store'), [
            'document_type' => 'TO',
            'direction' => 'INCOMING',
            'delivery_scope' => 'EXTERNAL',
            'originating_office' => $office->id,
            'subject' => 'Travel Order',
            'particulars' => 'Attend system rollout',
            'date_received' => '2026-04-15',
            'travel_order_type' => 'WITHIN_LA_UNION',
            'travel_dates' => 'April 15 to 16, 2026',
            'travelers' => "Alice Example\nBob Example",
            'destinations' => 'San Fernando, La Union',
        ]);

        $response->assertRedirect(route('documents.index', ['type' => 'TO']));

        $document = Document::firstOrFail();

        $this->assertSame('TO', $document->document_type);
        $this->assertSame('OUTGOING', $document->direction);
        $this->assertSame('INTERNAL', $document->delivery_scope);
        $this->assertSame('WITHIN_LA_UNION', $document->travel_order_type);
        $this->assertSame('April 15 to 16, 2026', $document->travel_dates);
        $this->assertSame("Alice Example\nBob Example", $document->travelers);
        $this->assertSame('San Fernando, La Union', $document->destinations);
        $this->assertSame('Attend system rollout', $document->subject);
    }

    public function test_travel_order_detail_page_shows_travel_fields(): void
    {
        $user = $this->createUser();
        $office = $this->createOffice('PICTO', 'Provincial Information and Communications Technology Office');

        $document = Document::create([
            'dts_number' => 'PICTO-WTO-2026-0001',
            'doc_number' => 'PICTO-PICTO-TO-2026-000001',
            'document_type' => 'TO',
            'direction' => 'OUTGOING',
            'delivery_scope' => 'INTERNAL',
            'travel_order_type' => 'SPECIAL_ORDER',
            'travel_dates' => 'April 20, 2026',
            'travelers' => 'Clarisahaina Gonting',
            'destinations' => 'La Union Capitol',
            'originating_office' => $office->id,
            'current_office' => $office->id,
            'current_holder' => $user->id,
            'subject' => 'Travel Order',
            'particulars' => 'Present the project status',
            'date_received' => '2026-04-20',
            'status' => 'ONGOING',
            'received_via_online' => false,
            'encoded_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('documents.show', $document));

        $response->assertOk();
        $response->assertSee('Travel Order Type');
        $response->assertSee('SPECIAL ORDER');
        $response->assertSee('Date/s of Travel');
        $response->assertSee('Clarisahaina Gonting');
        $response->assertSee('La Union Capitol');
    }
}
