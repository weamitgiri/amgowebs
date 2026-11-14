<?php

namespace Tests\Feature;

use App\Mail\AdminInquiryNotification;
use App\Mail\CustomerInquiryConfirmation;
use App\Models\PackageInquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InquiryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCms();
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'John Smith',
            'email' => 'john@example.com',
            'country' => 'United States',
            'description' => 'I need a professional business website with a contact form.',
            'consent' => '1',
        ], $overrides);
    }

    public function test_valid_inquiry_is_stored_and_confirmed(): void
    {
        Mail::fake();

        $response = $this->post(route('inquiry.store'), $this->validPayload());

        $inquiry = PackageInquiry::first();
        $this->assertNotNull($inquiry);
        $this->assertStringStartsWith('INQ-', $inquiry->reference);
        $this->assertSame('new', $inquiry->status);
        $this->assertSame(1, $inquiry->statusHistory()->count());

        $response->assertRedirect(route('inquiry.thanks', $inquiry->reference));

        Mail::assertQueued(CustomerInquiryConfirmation::class);
        Mail::assertQueued(AdminInquiryNotification::class);
    }

    public function test_inquiry_requires_core_fields(): void
    {
        $this->post(route('inquiry.store'), [])
            ->assertSessionHasErrors(['name', 'email', 'country', 'description', 'consent']);

        $this->assertSame(0, PackageInquiry::count());
    }

    public function test_honeypot_blocks_bots(): void
    {
        $this->post(route('inquiry.store'), $this->validPayload(['nickname' => 'i-am-a-bot']))
            ->assertSessionHasErrors('nickname');

        $this->assertSame(0, PackageInquiry::count());
    }

    public function test_thank_you_page_shows_reference(): void
    {
        $this->post(route('inquiry.store'), $this->validPayload());
        $inquiry = PackageInquiry::first();

        $this->get(route('inquiry.thanks', $inquiry->reference))
            ->assertOk()
            ->assertSee($inquiry->reference);
    }
}
