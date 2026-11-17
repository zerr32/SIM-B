<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\AccessLog;
use App\Models\Item;
use App\Models\LoginLog;
use App\Models\Mutation;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class DashboardCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_getting_items_redirects_to_the_dashboard_inventory(): void
    {
        $this->actingAs($this->createUser())
            ->get(route('items.index'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_dashboard_can_create_item_with_new_category_and_supplier(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $response = $this->from(route('dashboard'))->post(route('items.store'), [
            'name' => 'Laptop',
            'category_id' => 'NEW',
            'category_new' => 'Elektronik',
            'supplier_id' => 'NEW',
            'supplier_new_name' => 'PT Contoh',
            'supplier_new_email' => 'supplier@example.test',
            'supplier_new_phone' => '081234567890',
            'unit' => 'renceng',
            'stock' => 3,
            'purchase_price' => '1.000.000',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('categories', ['name' => 'Elektronik']);
        $this->assertDatabaseHas('suppliers', [
            'name' => 'PT Contoh',
            'phone' => '6281234567890',
        ]);

        $item = Item::with(['category', 'supplier'])->firstOrFail();
        $this->assertSame('Elektronik', $item->category->name);
        $this->assertSame('PT Contoh', $item->supplier->name);
        $this->assertSame('renceng', $item->unit);
        $this->assertSame('1000000.00', $item->purchase_price);
        $this->assertDatabaseHas('mutations', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'type' => 'IN',
            'unit' => 'renceng',
            'quantity' => 3,
            'unit_price' => '1000000.00',
            'total_price' => '3000000.00',
            'remaining_value' => '3000000.00',
        ]);
        $this->assertDatabaseHas('access_logs', [
            'user_id' => $user->id,
            'action_type' => 'CREATE_SUPPLIER',
            'description' => 'test-owner menambahkan supplier PT Contoh',
        ]);
        $this->assertDatabaseMissing('access_logs', [
            'user_id' => $user->id,
            'action_type' => 'CREATE_ITEM',
        ]);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Rp 1.000.000')
            ->assertSee('Filter')
            ->assertSee('Jenis Barang');
    }

    public function test_dashboard_profile_shows_authenticated_user_details(): void
    {
        $user = $this->createUser();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('<span class="block truncate font-semibold">'.$user->name.'</span>', false)
            ->assertSee('<span class="block text-xs capitalize text-slate-500">'.$user->role.'</span>', false)
            ->assertSee('Profil pengguna')
            ->assertSee($user->name)
            ->assertSee($user->email)
            ->assertSee($user->phone)
            ->assertSee('owner')
            ->assertSee('Buka profil');
    }

    public function test_item_creation_works_with_existing_category_and_supplier(): void
    {
        $user = $this->createUser();
        $category = Category::create(['name' => 'Peralatan']);
        $supplier = Supplier::create([
            'name' => 'Supplier Lama',
            'phone' => '6281234567891',
        ]);

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->post(route('items.store'), [
                'name' => 'Kursi',
                'category_id' => $category->id,
                'supplier_id' => $supplier->id,
                'unit' => 'pcs',
                'stock' => 0,
                'purchase_price' => '250.000',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('items', [
            'name' => 'Kursi',
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'purchase_price' => '250000.00',
        ]);
        $this->assertDatabaseCount('mutations', 0);
    }

    public function test_supplier_can_be_created_updated_and_deleted(): void
    {
        $this->actingAs($this->createUser());

        $this->from(route('dashboard'))->post(route('suppliers.store'), [
            'name' => 'Supplier Baru',
            'email' => 'new@example.test',
            'phone' => '081234567892',
            'address' => 'Jakarta',
        ])->assertRedirect(route('dashboard'));

        $supplier = Supplier::where('name', 'Supplier Baru')->firstOrFail();
        $this->assertSame('6281234567892', $supplier->phone);
        $user = auth()->user();
        $this->assertDatabaseHas('access_logs', [
            'user_id' => $user->id,
            'action_type' => 'CREATE_SUPPLIER',
            'description' => 'test-owner menambahkan supplier Supplier Baru',
        ]);

        $this->from(route('dashboard'))->patch(route('suppliers.update', $supplier), [
            'name' => 'Supplier Diperbarui',
            'email' => 'updated@example.test',
            'phone' => '081234567893',
            'address' => 'Bandung',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Supplier Diperbarui',
            'phone' => '6281234567893',
        ]);
        $this->assertDatabaseHas('access_logs', [
            'user_id' => $user->id,
            'action_type' => 'UPDATE_SUPPLIER',
            'description' => 'test-owner memperbarui supplier Supplier Diperbarui',
        ]);

        $category = Category::create(['name' => 'Kategori']);
        $item = Item::create([
            'code' => 'TEST-001',
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
            'name' => 'Item Terhubung',
            'stock' => 0,
            'purchase_price' => 100,
        ]);

        $this->from(route('dashboard'))->delete(route('suppliers.destroy', $supplier))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
        $this->assertDatabaseHas('items', ['id' => $item->id, 'supplier_id' => null]);
        $this->assertDatabaseHas('access_logs', [
            'user_id' => $user->id,
            'action_type' => 'DELETE_SUPPLIER',
            'description' => 'test-owner menghapus supplier Supplier Diperbarui',
        ]);
    }

    public function test_item_can_be_updated_deleted_and_mutation_history_is_preserved(): void
    {
        $user = $this->createUser();
        $category = Category::create(['name' => 'Kategori']);
        $item = Item::create([
            'code' => 'TEST-002',
            'category_id' => $category->id,
            'name' => 'Item Lama',
            'stock' => 2,
            'purchase_price' => 100,
        ]);

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->patch(route('items.update', $item), [
                'name' => 'Item Baru',
                'category_id' => $category->id,
                'supplier_id' => '',
                'unit' => 'box',
                'purchase_price' => '1.250.000',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'name' => 'Item Baru',
            'unit' => 'box',
            'purchase_price' => '1250000.00',
        ]);
        $this->assertDatabaseHas('access_logs', [
            'user_id' => $user->id,
            'action_type' => 'UPDATE_ITEM',
            'description' => 'test-owner memperbarui barang TEST-002 - Item Baru',
        ]);

        $this->from(route('dashboard'))
            ->post(route('mutations.store'), [
                'item_id' => $item->id,
                'type' => 'OUT',
                'quantity' => 1,
                'recipient_name' => 'Budi Penerima',
                'notes' => 'Dipakai',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('items', ['id' => $item->id, 'stock' => 1]);
        $this->assertSame(1, Mutation::where('item_id', $item->id)->where('type', 'OUT')->count());
        $this->assertDatabaseHas('mutations', [
            'item_id' => $item->id,
            'type' => 'OUT',
            'unit' => 'box',
            'recipient_name' => 'Budi Penerima',
            'unit_price' => '1250000.00',
            'total_price' => '1250000.00',
            'remaining_value' => '1250000.00',
        ]);

        $this->from(route('dashboard'))->delete(route('items.destroy', $item))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('items', ['id' => $item->id]);
        $this->assertDatabaseHas('mutations', [
            'item_id' => null,
            'item_code' => 'TEST-002',
            'item_name' => 'Item Baru',
            'type' => 'OUT',
            'notes' => 'Dipakai',
        ]);
        $this->assertDatabaseMissing('mutations', [
            'item_code' => 'TEST-002',
            'type' => 'DELETE',
        ]);
        $this->assertDatabaseHas('access_logs', [
            'user_id' => $user->id,
            'action_type' => 'DELETE_ITEM',
            'description' => 'test-owner menghapus barang TEST-002 - Item Baru',
        ]);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('TEST-002 Item Baru')
            ->assertSee('Budi Penerima')
            ->assertSee('Karyawan')
            ->assertSee('Petugas')
            ->assertSee('test-owner menghapus barang TEST-002 - Item Baru');
    }

    public function test_incoming_mutation_uses_item_master_price(): void
    {
        $user = $this->createUser();
        $category = Category::create(['name' => 'Kategori']);
        $item = Item::create([
            'code' => 'TEST-003',
            'category_id' => $category->id,
            'name' => 'Item Masuk',
            'stock' => 2,
            'purchase_price' => 100,
        ]);

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->post(route('mutations.store'), [
                'item_id' => $item->id,
                'type' => 'IN',
                'quantity' => 3,
            ])
            ->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Total Nilai Mutasi')
            ->assertSee('Sisa Value Aset')
            ->assertSeeInOrder(['Total Nilai Mutasi', 'Stok Akhir', 'Sisa Value Aset'])
            ->assertSee('Rp 100')
            ->assertSee('Rp 300')
            ->assertSee('Rp 500');

        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'stock' => 5,
            'purchase_price' => '100.00',
        ]);
        $this->assertDatabaseHas('mutations', [
            'item_id' => $item->id,
            'type' => 'IN',
            'unit' => 'pcs',
            'unit_price' => '100.00',
            'total_price' => '300.00',
            'remaining_value' => '500.00',
        ]);
    }

    public function test_outgoing_mutation_requires_the_name_of_the_receiving_officer(): void
    {
        $category = Category::create(['name' => 'Kategori']);
        $item = Item::create([
            'code' => 'TEST-005',
            'category_id' => $category->id,
            'name' => 'Item Keluar',
            'unit' => 'box',
            'stock' => 2,
            'purchase_price' => 100,
        ]);

        $this->actingAs($this->createUser())
            ->from(route('dashboard'))
            ->post(route('mutations.store'), [
                'item_id' => $item->id,
                'type' => 'OUT',
                'quantity' => 1,
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasErrors('recipient_name');

        $this->assertDatabaseHas('items', ['id' => $item->id, 'stock' => 2]);
        $this->assertDatabaseMissing('mutations', ['item_id' => $item->id, 'type' => 'OUT']);
    }

    public function test_incoming_mutation_ignores_submitted_price_and_uses_item_master_price(): void
    {
        $user = $this->createUser();
        $category = Category::create(['name' => 'Kategori']);
        $item = Item::create([
            'code' => 'TEST-004',
            'category_id' => $category->id,
            'name' => 'Item Tanpa Harga',
            'stock' => 1,
            'purchase_price' => 100,
        ]);

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->post(route('mutations.store'), [
                'item_id' => $item->id,
                'type' => 'IN',
                'quantity' => 1,
                'unit_price' => '25.000',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('items', ['id' => $item->id, 'stock' => 2]);
        $this->assertDatabaseHas('mutations', [
            'item_id' => $item->id,
            'type' => 'IN',
            'unit_price' => '100.00',
            'total_price' => '100.00',
        ]);
    }

    public function test_owner_and_admin_can_create_admin_but_admin_cannot_create_owner(): void
    {
        $owner = $this->createUser();
        $this->actingAs($owner)
            ->from(route('dashboard'))
            ->post(route('users.store'), [
                'name' => 'Admin Baru',
                'username' => 'admin-baru',
                'email' => 'admin-baru@example.test',
                'phone' => '081234567894',
                'password' => 'secret123',
                'role' => 'admin',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'username' => 'admin-baru',
            'phone' => '6281234567894',
            'role' => 'admin',
        ]);

        $admin = User::create([
            'name' => 'Test Admin',
            'username' => 'test-admin',
            'email' => 'admin@example.test',
            'phone' => '6281234567895',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->from(route('dashboard'))
            ->post(route('users.store'), [
            'name' => 'Admin Tambahan',
            'username' => 'admin-tambahan',
            'email' => 'admin-tambahan@example.test',
            'phone' => '081234567896',
            'password' => 'secret123',
            'role' => 'admin',
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', ['username' => 'admin-tambahan', 'role' => 'admin']);

        $this->post(route('users.store'), [
            'name' => 'Owner Baru',
            'username' => 'owner-baru',
            'email' => 'owner-baru@example.test',
            'phone' => '081234567897',
            'password' => 'secret123',
            'role' => 'owner',
        ])->assertSessionHasErrors('role');

        $this->post(route('users.store'), [
            'name' => 'Petugas Lama',
            'username' => 'petugas-lama',
            'email' => 'petugas-lama@example.test',
            'phone' => '081234567898',
            'password' => 'secret123',
            'role' => 'petugas',
        ])->assertSessionHasErrors('role');
    }

    public function test_user_factory_provides_all_required_user_columns(): void
    {
        $user = User::factory()->create();

        $this->assertNotEmpty($user->username);
        $this->assertMatchesRegularExpression('/^\d{8,15}$/', $user->phone);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_admin_dashboard_only_exposes_allowed_sections_and_owner_only_logs(): void
    {
        $admin = User::create([
            'name' => 'Dashboard Admin',
            'username' => 'dashboard-admin',
            'email' => 'dashboard-admin@example.test',
            'phone' => '6281234567803',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
        LoginLog::create([
            'user_id' => null,
            'username_input' => 'private-login-name',
            'status' => 'FAILED',
        ]);
        AccessLog::create([
            'user_id' => $admin->id,
            'action_type' => 'PRIVATE_TEST',
            'description' => 'private-activity-secret',
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Manajemen User')
            ->assertSee('Mutasi Stok')
            ->assertSee('Barang &amp; Stok', false)
            ->assertSee('data-tab="suppliers"', false)
            ->assertDontSee('Log Login')
            ->assertDontSee('Log Aktivitas')
            ->assertDontSee('private-login-name')
            ->assertDontSee('private-activity-secret');

        $this->post(route('suppliers.store'), [
            'name' => 'Admin Supplier',
            'phone' => '081234567899',
        ])->assertRedirect();
        $this->assertDatabaseHas('suppliers', ['name' => 'Admin Supplier']);

        $owner = $this->createUser();
        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Log Login')
            ->assertSee('Log Aktivitas')
            ->assertSee('private-login-name')
            ->assertSee('private-activity-secret');
    }

    public function test_admin_can_edit_another_admin_account_details(): void
    {
        $admin = User::create([
            'name' => 'Managing Admin',
            'username' => 'managing-admin',
            'email' => 'managing-admin@example.test',
            'phone' => '6281234567804',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
        $target = User::create([
            'name' => 'Old Admin Name',
            'username' => 'editable-admin',
            'email' => 'old-admin@example.test',
            'phone' => '6281234567805',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->from(route('dashboard'))
            ->patch(route('users.update', $target), [
                'name' => 'Updated Admin Name',
                'email' => 'updated-admin@example.test',
                'phone' => '081234567805',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'name' => 'Updated Admin Name',
            'email' => 'updated-admin@example.test',
            'phone' => '6281234567805',
            'role' => 'admin',
        ]);

        $this->patch(route('users.update', $target), [
            'name' => 'Updated Admin Name',
            'email' => 'updated-admin@example.test',
            'phone' => '6281234567805',
            'role' => 'owner',
        ])->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $target->id, 'role' => 'admin']);
        $this->from(route('dashboard'))
            ->delete(route('users.destroy', $target))
            ->assertRedirect(route('dashboard'));
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_admin_can_update_own_name_email_and_phone_without_changing_role(): void
    {
        $admin = User::create([
            'name' => 'Admin Lama',
            'username' => 'admin-profile',
            'email' => 'admin-profile@example.test',
            'phone' => '6281234567816',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->from(route('dashboard'))
            ->patch(route('users.update', $admin), [
                'name' => 'Admin Diperbarui',
                'email' => 'admin-updated@example.test',
                'phone' => '081234567816',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Admin Diperbarui',
            'email' => 'admin-updated@example.test',
            'phone' => '6281234567816',
            'role' => 'admin',
        ]);
    }

    public function test_password_form_uses_eye_icons_for_password_inputs(): void
    {
        $admin = User::create([
            'name' => 'Eye Test Admin',
            'username' => 'eye-test-admin',
            'email' => 'eye-test-admin@example.test',
            'phone' => '6281234567817',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('fa-eye', false)
            ->assertSee('fa-eye-slash', false)
            ->assertDontSee('current_password')
            ->assertDontSee('Password saat ini')
            ->assertDontSee('x-text="showNew ?', false)
            ->assertSee('title="Ubah data akun"', false);
    }

    public function test_admin_can_delete_any_other_admin_and_action_is_audited(): void
    {
        $admin = User::create([
            'name' => 'Creating Admin',
            'username' => 'creating-admin',
            'email' => 'creating-admin@example.test',
            'phone' => '6281234567813',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);
        $ownAccount = User::create([
            'name' => 'Created Admin',
            'username' => 'created-admin',
            'email' => 'created-admin@example.test',
            'phone' => '6281234567814',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'created_by' => $admin->id,
        ]);
        $otherAccount = User::create([
            'name' => 'Other Created Admin',
            'username' => 'other-created-admin',
            'email' => 'other-created-admin@example.test',
            'phone' => '6281234567815',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->from(route('dashboard'))
            ->delete(route('users.destroy', $otherAccount))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('users', ['id' => $otherAccount->id]);
        $this->assertDatabaseHas('access_logs', [
            'user_id' => $admin->id,
            'target_user_id' => null,
            'action_type' => 'DELETE_USER',
            'description' => 'creating-admin menghapus akun @other-created-admin',
        ]);

        $this->from(route('dashboard'))
            ->delete(route('users.destroy', $ownAccount))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('users', ['id' => $ownAccount->id]);
        $this->assertDatabaseHas('access_logs', [
            'user_id' => $admin->id,
            'target_user_id' => null,
            'action_type' => 'DELETE_USER',
            'description' => 'creating-admin menghapus akun @created-admin',
        ]);
    }

    public function test_admin_can_change_own_and_another_admin_passwords_and_activity_is_logged(): void
    {
        $admin = User::create([
            'name' => 'Test Admin',
            'username' => 'password-admin',
            'email' => 'password-admin@example.test',
            'phone' => '6281234567801',
            'password' => Hash::make('old-password'),
            'role' => 'admin',
        ]);
        $otherAdmin = User::create([
            'name' => 'Other Admin',
            'username' => 'password-other-admin',
            'email' => 'password-other-admin@example.test',
            'phone' => '6281234567802',
            'password' => Hash::make('old-password'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->from(route('dashboard'))
            ->put(route('users.password', $admin), [
                'password' => 'admin-new-password',
                'password_confirmation' => 'admin-new-password',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertTrue(Hash::check('admin-new-password', $admin->fresh()->password));
        $this->assertDatabaseHas('access_logs', [
            'user_id' => $admin->id,
            'target_user_id' => $admin->id,
            'action_type' => 'CHANGE_PASSWORD',
        ]);

        $this->from(route('dashboard'))->put(route('users.password', $otherAdmin), [
            'password' => 'admin-new-password',
            'password_confirmation' => 'admin-new-password',
        ])->assertRedirect(route('dashboard'));

        $this->assertTrue(Hash::check('admin-new-password', $otherAdmin->fresh()->password));
        $this->assertDatabaseHas('access_logs', [
            'user_id' => $admin->id,
            'target_user_id' => $otherAdmin->id,
            'action_type' => 'CHANGE_PASSWORD',
        ]);
    }

    public function test_admin_can_change_another_admin_password_but_not_owner_password(): void
    {
        $admin = User::create([
            'name' => 'Test Admin',
            'username' => 'limited-admin',
            'email' => 'limited-admin@example.test',
            'phone' => '6281234567811',
            'password' => Hash::make('old-password'),
            'role' => 'admin',
        ]);
        $otherAdmin = User::create([
            'name' => 'Other Admin',
            'username' => 'other-admin',
            'email' => 'other-admin@example.test',
            'phone' => '6281234567812',
            'password' => Hash::make('old-password'),
            'role' => 'admin',
        ]);
        $owner = $this->createUser();

        $this->actingAs($admin);

        $this->from(route('dashboard'))->put(route('users.password', $otherAdmin), [
            'password' => 'admin-new-password',
            'password_confirmation' => 'admin-new-password',
        ])->assertRedirect(route('dashboard'));

        $this->from(route('dashboard'))->put(route('users.password', $owner), [
            'password' => 'attempted-new-password',
            'password_confirmation' => 'attempted-new-password',
        ])->assertForbidden();

        $this->assertTrue(Hash::check('admin-new-password', $otherAdmin->fresh()->password));
        $this->assertTrue(Hash::check('password123', $owner->fresh()->password));
        $this->assertDatabaseHas('access_logs', [
            'user_id' => $admin->id,
            'target_user_id' => $otherAdmin->id,
            'action_type' => 'CHANGE_PASSWORD',
        ]);
    }

    public function test_owner_can_change_passwords_for_all_accounts(): void
    {
        $owner = $this->createUser();
        $admin = User::create([
            'name' => 'Target Admin',
            'username' => 'owner-target-admin',
            'email' => 'owner-target-admin@example.test',
            'phone' => '6281234567821',
            'password' => Hash::make('old-password'),
            'role' => 'admin',
        ]);
        $otherAdmin = User::create([
            'name' => 'Another Target Admin',
            'username' => 'owner-target-admin-2',
            'email' => 'owner-target-admin-2@example.test',
            'phone' => '6281234567822',
            'password' => Hash::make('old-password'),
            'role' => 'admin',
        ]);

        $this->actingAs($owner);

        foreach ([$owner, $admin, $otherAdmin] as $target) {
            $passwordData = [
                'password' => 'owner-changed-password',
                'password_confirmation' => 'owner-changed-password',
            ];

            $this->from(route('dashboard'))
                ->put(route('users.password', $target), $passwordData)
                ->assertRedirect(route('dashboard'));
            $this->assertTrue(Hash::check('owner-changed-password', $target->fresh()->password));
            $this->assertDatabaseHas('access_logs', [
                'user_id' => $owner->id,
                'target_user_id' => $target->id,
                'action_type' => 'CHANGE_PASSWORD',
            ]);
        }
    }

    public function test_deleting_user_permanently_removes_row_and_preserves_audit_history(): void
    {
        $owner = $this->createUser();
        $target = User::create([
            'name' => 'Admin Dihapus',
            'username' => 'admin-dihapus',
            'email' => 'admin-dihapus@example.test',
            'phone' => '6281234567831',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'created_by' => $owner->id,
        ]);
        $child = User::create([
            'name' => 'Akun Bawahan',
            'username' => 'akun-bawahan',
            'email' => 'akun-bawahan@example.test',
            'phone' => '6281234567832',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'created_by' => $target->id,
        ]);
        $category = Category::create(['name' => 'Audit']);
        $item = Item::create([
            'code' => 'AUDIT-001',
            'category_id' => $category->id,
            'name' => 'Barang Audit',
            'stock' => 1,
            'purchase_price' => 100,
        ]);
        Mutation::create([
            'item_id' => $item->id,
            'user_id' => $target->id,
            'type' => 'IN',
            'quantity' => 1,
            'previous_stock' => 0,
            'current_stock' => 1,
            'notes' => 'Riwayat harus tetap ada',
        ]);
        LoginLog::create([
            'user_id' => $target->id,
            'username_input' => $target->username,
            'status' => 'SUCCESS',
        ]);
        AccessLog::create([
            'user_id' => $target->id,
            'target_user_id' => $target->id,
            'action_type' => 'TEST_AUDIT',
            'description' => 'Riwayat sebelum penghapusan',
        ]);
        $target->createToken('test-token');

        $this->actingAs($owner)
            ->from(route('dashboard'))
            ->delete(route('users.destroy', $target))
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
        $this->assertDatabaseHas('users', ['id' => $child->id, 'created_by' => null]);
        $this->assertDatabaseHas('mutations', [
            'item_id' => $item->id,
            'user_id' => null,
            'notes' => 'Riwayat harus tetap ada',
        ]);
        $this->assertDatabaseHas('login_logs', [
            'user_id' => null,
            'username_input' => 'admin-dihapus',
            'status' => 'SUCCESS',
        ]);
        $this->assertDatabaseHas('access_logs', [
            'user_id' => null,
            'target_user_id' => null,
            'action_type' => 'TEST_AUDIT',
            'description' => 'Riwayat sebelum penghapusan',
        ]);
        $this->assertDatabaseHas('access_logs', [
            'user_id' => $owner->id,
            'target_user_id' => null,
            'action_type' => 'DELETE_USER',
            'description' => 'test-owner menghapus akun @admin-dihapus',
        ]);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $target->id,
        ]);
    }

    private function createUser(): User
    {
        return User::create([
            'name' => 'Test Owner',
            'username' => 'test-owner',
            'email' => 'owner@example.test',
            'phone' => '6281234567890',
            'password' => Hash::make('password123'),
            'role' => 'owner',
        ]);
    }
}
