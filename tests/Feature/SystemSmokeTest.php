<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\FinancialTransaction;
use App\Models\Property;
use App\Models\User;
use App\Services\BookingService;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SystemSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_public_pages_render(): void
    {
        $this->get('/')->assertOk();
        $this->get('/properties')->assertOk();
        $this->get('/about')->assertOk();
        $this->get('/contact')->assertOk();
        $this->get('/login')->assertOk();
    }

    public function test_admin_requires_authentication(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_admin_can_open_dashboard(): void
    {
        $admin = User::query()->where('email', 'admin@boshret.test')->firstOrFail();

        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/properties')->assertOk();
        $this->actingAs($admin)->get('/admin/customers')->assertOk();
        $this->actingAs($admin)->get('/admin/bookings')->assertOk();
        $this->actingAs($admin)->get('/admin/sales')->assertOk();
        $this->actingAs($admin)->get('/admin/payments')->assertOk();
        $this->actingAs($admin)->get('/admin/expenses')->assertOk();
        $this->actingAs($admin)->get('/admin/accounts')->assertOk();
        $this->actingAs($admin)->get('/admin/reports')->assertOk();
        $this->actingAs($admin)->get('/admin/users')->assertOk();
        $this->actingAs($admin)->get('/admin/roles')->assertOk();
        $this->actingAs($admin)->get('/admin/settings')->assertOk();
    }

    public function test_staff_cannot_open_users_page(): void
    {
        $staff = User::query()->where('email', 'staff@boshret.test')->firstOrFail();

        $this->actingAs($staff)->get('/admin/users')->assertForbidden();
    }

    public function test_booking_overlap_is_blocked(): void
    {
        $existing = Booking::query()->where('code', 'حجز-00001')->firstOrFail();
        $customer = Customer::query()->firstOrFail();

        $this->expectException(ValidationException::class);

        app(BookingService::class)->create([
            'property_id' => $existing->property_id,
            'customer_id' => $customer->id,
            'start_date' => $existing->start_date->toDateString(),
            'end_date' => $existing->end_date->toDateString(),
            'nightly_rate' => 1000,
            'status' => 'confirmed',
        ]);
    }

    public function test_completed_sale_marks_property_as_sold(): void
    {
        $admin = User::query()->where('email', 'admin@boshret.test')->firstOrFail();
        $this->actingAs($admin);

        $property = Property::query()->where('status', 'available')->where('purpose', 'sale')->firstOrFail();
        $customer = Customer::query()->firstOrFail();

        $sale = app(SaleService::class)->create([
            'property_id' => $property->id,
            'customer_id' => $customer->id,
            'sale_price' => 100000,
            'discount' => 0,
            'sale_date' => now()->toDateString(),
            'status' => 'completed',
            'payment_method' => 'cash',
        ]);

        $this->assertSame('sold', $property->fresh()->status);
        $this->assertTrue(
            FinancialTransaction::query()
                ->where('reference_type', $sale::class)
                ->where('reference_id', $sale->id)
                ->where('type', 'sale_income')
                ->exists()
        );
    }
}
