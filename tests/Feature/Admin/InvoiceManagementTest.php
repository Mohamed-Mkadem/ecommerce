<?php

namespace Tests\Feature\Admin;

use App\Models\Invoice;
use App\Models\Invoiceable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class InvoiceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_revenue_and_expense_invoices_with_dinar_amounts(): void
    {
        $admin = $this->makeUser('admin');

        $this->actingAs($admin)->post(route('invoices.store'), [
            'title' => 'Ramadan campaign',
            'category' => 'marketing',
            'type' => 'expense',
            'amount' => 125.75,
            'description' => 'Social media promotion',
        ])->assertRedirect();

        $expense = Invoice::query()->firstOrFail();
        $this->assertSame('Ramadan campaign', $expense->title);
        $this->assertSame(125750, $expense->amount);
        $this->assertSame('marketing', $expense->category);
        $this->assertSame('expense', $expense->type);
        $this->assertSame('Social media promotion', $expense->description);
        $this->assertInstanceOf(Invoiceable::class, $expense->invoiceable);

        $this->actingAs($admin)->post(route('invoices.store'), [
            'title' => 'Wholesale order revenue',
            'category' => 'daily sales',
            'type' => 'revenue',
            'amount' => 900,
        ])->assertRedirect();

        $this->assertDatabaseHas('invoices', [
            'title' => 'Wholesale order revenue',
            'category' => 'daily sales',
            'type' => 'revenue',
            'amount' => 900000,
            'description' => null,
        ]);
    }

    public function test_invoice_creation_validates_unique_title_type_and_positive_amount(): void
    {
        $admin = $this->makeUser('admin');
        Invoice::query()->create([
            'title' => 'Existing invoice',
            'category' => 'operating costs',
            'type' => 'expense',
            'amount' => 10000,
            'invoiceable_type' => Invoiceable::class,
            'invoiceable_id' => Invoiceable::query()->create()->id,
        ]);

        $this->actingAs($admin)->postJson(route('invoices.store'), [
            'title' => 'Existing invoice',
            'category' => 'other',
            'type' => 'income',
            'amount' => 0,
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'type', 'amount']);

        $this->assertSame(1, Invoice::query()->count());
    }

    public function test_admin_can_update_invoice_details_and_keep_its_own_unique_title(): void
    {
        $admin = $this->makeUser('admin');
        $invoiceable = Invoiceable::query()->create();
        $invoice = Invoice::query()->create([
            'title' => 'Initial expense',
            'category' => 'operating costs',
            'type' => 'expense',
            'amount' => 25000,
            'description' => null,
            'invoiceable_type' => Invoiceable::class,
            'invoiceable_id' => $invoiceable->id,
        ]);

        $this->actingAs($admin)->put(route('invoices.update', $invoice), [
            'title' => 'Initial expense',
            'category' => 'payments',
            'type' => 'revenue',
            'amount' => 70.5,
            'description' => 'Updated invoice description',
        ])->assertRedirect();

        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'title' => 'Initial expense',
            'category' => 'payments',
            'type' => 'revenue',
            'amount' => 70500,
            'description' => 'Updated invoice description',
        ]);
    }

    public function test_invoice_list_supports_title_type_category_and_amount_filters(): void
    {
        $admin = $this->makeUser('admin');
        $matching = $this->makeInvoice('Packaging supply', 'expense', 'operating costs', 45000);
        $this->makeInvoice('Ad campaign', 'expense', 'marketing', 60000);
        $this->makeInvoice('Store sales', 'revenue', 'daily sales', 90000);

        $this->actingAs($admin)->get(route('invoices.index', [
            'search' => 'Packaging',
            'type' => 'expense',
            'category' => 'operating costs',
            'minAmount' => 40,
            'maxAmount' => 50,
        ]))->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Invoices/Index')
                ->where('invoices.data.0.id', $matching->id)
                ->where('invoices.data.0.amount', '45.000')
                ->has('invoices.data', 1)
            );
    }

    public function test_invoice_details_are_viewable_and_invoice_can_be_deleted(): void
    {
        $admin = $this->makeUser('admin');
        $invoice = $this->makeInvoice('Delivery packaging', 'expense', 'operating costs', 14500);

        $this->actingAs($admin)->get(route('invoices.show', $invoice))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Invoices/Show')
                ->where('invoice.data.id', $invoice->id)
                ->where('invoice.data.amount', '14.500')
                ->where('invoice.data.title', 'Delivery packaging')
            );

        $this->actingAs($admin)->delete(route('invoices.destroy', $invoice))->assertRedirect();
        $this->assertDatabaseMissing('invoices', ['id' => $invoice->id]);
    }

    public function test_invoice_management_is_admin_only(): void
    {
        $moderator = $this->makeUser('moderator');
        $invoice = $this->makeInvoice('Restricted invoice', 'expense', 'other', 10000);

        $this->actingAs($moderator)->get(route('invoices.index'))->assertForbidden();
        $this->actingAs($moderator)->get(route('invoices.create'))->assertForbidden();
        $this->actingAs($moderator)->postJson(route('invoices.store'), [])->assertForbidden();
        $this->actingAs($moderator)->get(route('invoices.show', $invoice))->assertForbidden();
        $this->actingAs($moderator)->get(route('invoices.edit', $invoice))->assertForbidden();
        $this->actingAs($moderator)->putJson(route('invoices.update', $invoice), [])->assertForbidden();
        $this->actingAs($moderator)->deleteJson(route('invoices.destroy', $invoice))->assertForbidden();
    }

    private function makeInvoice(string $title, string $type, string $category, int $amount): Invoice
    {
        $invoiceable = Invoiceable::query()->create();

        return Invoice::query()->create([
            'title' => $title,
            'category' => $category,
            'type' => $type,
            'amount' => $amount,
            'invoiceable_type' => Invoiceable::class,
            'invoiceable_id' => $invoiceable->id,
        ]);
    }

    private function makeUser(string $role): User
    {
        return User::query()->create([
            'first_name' => 'Invoice',
            'last_name' => ucfirst($role),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => $role,
            'status' => 'active',
        ]);
    }
}
