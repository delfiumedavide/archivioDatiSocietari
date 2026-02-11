<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $viewer;

    protected function setUp(): void
    {
        parent::setUp();

        $adminRole = Role::create(['name' => 'admin', 'label' => 'Amministratore']);
        $viewerRole = Role::create(['name' => 'viewer', 'label' => 'Visualizzatore']);

        $companyViewPerm = Permission::create(['name' => 'companies.view', 'section' => 'companies', 'label' => 'Visualizza']);
        $companyCreatePerm = Permission::create(['name' => 'companies.create', 'section' => 'companies', 'label' => 'Crea']);

        $this->admin = User::factory()->create();
        $this->admin->roles()->attach($adminRole);

        $this->viewer = User::factory()->create();
        $this->viewer->roles()->attach($viewerRole);
        $this->viewer->permissions()->attach($companyViewPerm);
    }

    public function test_admin_can_view_companies_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('companies.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_company(): void
    {
        $response = $this->actingAs($this->admin)->post(route('companies.store'), [
            'denominazione' => 'Test SRL',
            'codice_fiscale' => '12345678901',
            'partita_iva' => '12345678901',
            'forma_giuridica' => 'SRL',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('companies', ['denominazione' => 'Test SRL']);
    }

    public function test_viewer_cannot_create_company(): void
    {
        $response = $this->actingAs($this->viewer)->post(route('companies.store'), [
            'denominazione' => 'Test SRL',
        ]);

        $response->assertForbidden();
    }

    public function test_company_requires_denominazione(): void
    {
        $response = $this->actingAs($this->admin)->post(route('companies.store'), [
            'denominazione' => '',
        ]);

        $response->assertSessionHasErrors('denominazione');
    }

    public function test_admin_can_delete_company(): void
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('companies.destroy', $company));
        $response->assertRedirect(route('companies.index'));
        $this->assertSoftDeleted('companies', ['id' => $company->id]);
    }
}
