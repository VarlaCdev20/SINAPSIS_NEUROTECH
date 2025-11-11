<?php

namespace Tests\Feature;

use App\Livewire\Solicitudes;
use App\Models\Bitacora;
use App\Models\Solicitante;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SolicitudesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Freeze time for deterministic Bitacora timestamps
        Carbon::setTestNow(Carbon::parse('2025-01-15 12:00:00'));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * 1) Should filter Solicitantes by partial search across multiple fields using case-insensitive like
     */
    public function test_filters_by_partial_search_across_multiple_fields()
    {
        // Arrange
        Solicitante::query()->insert([
            [
                'cod_sol' => 1,
                'nom_sol' => 'Juan',
                'ap_pat_sol' => 'Perez',
                'ap_mat_sol' => 'Lopez',
                'email_sol' => 'juan@example.com',
                'est_sol' => 'pendiente',
            ],
            [
                'cod_sol' => 2,
                'nom_sol' => 'Maria',
                'ap_pat_sol' => 'Gomez',
                'ap_mat_sol' => 'Diaz',
                'email_sol' => 'maria@example.com',
                'est_sol' => 'pendiente',
            ],
            [
                'cod_sol' => 3,
                'nom_sol' => 'Luis',
                'ap_pat_sol' => 'Ramirez',
                'ap_mat_sol' => 'Juarez',
                'email_sol' => 'luis@example.com',
                'est_sol' => 'pendiente',
            ],
        ]);

        // Act: search by last name partial that matches only second row
        $component = Livewire::test(Solicitudes::class)
            ->set('buscar', 'gom') // should match Gomez via ILIKE
            ->call('render');

        // Assert: render should include cod_sol 2 and exclude others by ordering desc
        $html = $component->lastRenderedDom();
        $this->assertStringContainsString('maria@example.com', $html);
        $this->assertStringNotContainsString('juan@example.com', $html);
        $this->assertStringNotContainsString('luis@example.com', $html);
    }

    /**
     * 2) Should limit the number of returned records when n_registros is set
     */
    public function test_limits_number_of_records_when_n_registros_is_set()
    {
        // Arrange: create 5 solicitantes with descending cod_sol order
        for ($i = 1; $i <= 5; $i++) {
            Solicitante::create([
                'cod_sol' => $i,
                'nom_sol' => 'Nombre'.$i,
                'ap_pat_sol' => 'ApellidoP'.$i,
                'ap_mat_sol' => 'ApellidoM'.$i,
                'email_sol' => "u{$i}@example.com",
                'est_sol' => 'pendiente',
            ]);
        }

        // Act
        $component = Livewire::test(Solicitudes::class)
            ->set('n_registros', 2)
            ->call('render');

        // Assert: only latest 2 by cod_sol desc should appear: 5 and 4
        $html = $component->lastRenderedDom();
        $this->assertStringContainsString('u5@example.com', $html);
        $this->assertStringContainsString('u4@example.com', $html);
        $this->assertStringNotContainsString('u3@example.com', $html);
        $this->assertStringNotContainsString('u2@example.com', $html);
        $this->assertStringNotContainsString('u1@example.com', $html);
    }

    /**
     * 3) Should dispatch confirmarAprobacion with correct cod_sol when aprobar is called
     */
    public function test_aprobar_dispatches_confirmar_aprobacion_event()
    {
        $component = Livewire::test(Solicitudes::class)
            ->call('aprobar', 123);

        // For Livewire v3, assertDispatched for browser events
        $component->assertDispatched('confirmarAprobacion', 123);
    }

    /**
     * 4) Should approve: update est_sol, create Bitacora, and dispatch success swal
     */
    public function test_confirmado_aprobacion_updates_status_creates_bitacora_and_dispatches_swal()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $sol = Solicitante::create([
            'cod_sol' => 55,
            'nom_sol' => 'Ana',
            'ap_pat_sol' => 'Torres',
            'ap_mat_sol' => 'Mendez',
            'email_sol' => 'ana@example.com',
            'est_sol' => 'pendiente',
        ]);

        // Act
        $component = Livewire::test(Solicitudes::class)
            ->call('confirmadoAprobacion', 55);

        // Assert DB changes
        $this->assertEquals('aprobado', $sol->fresh()->est_sol);

        // Assert Bitacora created with proper message
        $this->assertDatabaseHas('bitacora', [
            'acc_bit' => 'Aprobó la solicitud de '.$sol->getNombreCompletoAttribute(),
        ]);

        // Assert swal event dispatched with success icon and refresh flag
        $component->assertDispatched('swal', function ($payload) use ($sol) {
            return ($payload['icon'] ?? null) === 'success'
                && ($payload['title'] ?? null) === 'Solicitud aprobada'
                && ($payload['refresh'] ?? null) === true
                && str_contains($payload['text'] ?? '', $sol->getNombreCompletoAttribute());
        });
    }

    /**
     * 5) Should reject: update est_sol, create Bitacora, and dispatch info swal
     */
    public function test_confirmado_rechazo_updates_status_creates_bitacora_and_dispatches_swal()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $sol = Solicitante::create([
            'cod_sol' => 77,
            'nom_sol' => 'Pedro',
            'ap_pat_sol' => 'Lara',
            'ap_mat_sol' => 'Suarez',
            'email_sol' => 'pedro@example.com',
            'est_sol' => 'pendiente',
        ]);

        // Act
        $component = Livewire::test(Solicitudes::class)
            ->call('confirmadoRechazo', 77);

        // Assert DB changes
        $this->assertEquals('rechazado', $sol->fresh()->est_sol);

        // Assert Bitacora created
        $this->assertDatabaseHas('bitacora', [
            'acc_bit' => 'Rechazó la solicitud de '.$sol->getNombreCompletoAttribute(),
        ]);

        // Assert swal event dispatched with info icon
        $component->assertDispatched('swal', function ($payload) use ($sol) {
            return ($payload['icon'] ?? null) === 'info'
                && ($payload['title'] ?? null) === 'Solicitud rechazada'
                && ($payload['refresh'] ?? null) === true
                && str_contains($payload['text'] ?? '', $sol->getNombreCompletoAttribute());
        });
    }
    /**
     * 6) Should ignore non-numeric n_registros and return full ordered list
     */
    public function test_ignores_non_numeric_n_registros_and_returns_full_list()
    {
        // Arrange
        foreach ([10, 20, 30] as $i) {
            Solicitante::create([
                'cod_sol' => $i,
                'nom_sol' => 'N'.$i,
                'ap_pat_sol' => 'P'.$i,
                'ap_mat_sol' => 'M'.$i,
                'email_sol' => "n{$i}@ex.com",
                'est_sol' => 'pendiente',
            ]);
        }

        // Act: set non-numeric string
        $component = Livewire::test(Solicitudes::class)
            ->set('n_registros', 'dos')
            ->call('render');

        // Assert: all emails present in desc order snapshot
        $html = $component->lastRenderedDom();
        $this->assertStringContainsString('n30@ex.com', $html);
        $this->assertStringContainsString('n20@ex.com', $html);
        $this->assertStringContainsString('n10@ex.com', $html);
    }

    /**
     * 7) Should not create Bitacora when user is unauthenticated
     */
    public function test_no_bitacora_created_when_user_not_authenticated()
    {
        // Arrange no auth user
        $sol = Solicitante::create([
            'cod_sol' => 91,
            'nom_sol' => 'NoAuth',
            'ap_pat_sol' => 'User',
            'ap_mat_sol' => 'Case',
            'email_sol' => 'noauth@example.com',
            'est_sol' => 'pendiente',
        ]);

        // Act
        Livewire::test(Solicitudes::class)->call('confirmadoAprobacion', 91);

        // Assert: updated but no bitacora record
        $this->assertEquals('aprobado', $sol->fresh()->est_sol);
        $this->assertDatabaseMissing('bitacora', [
            'acc_bit' => 'Aprobó la solicitud de '.$sol->getNombreCompletoAttribute(),
        ]);
    }

    /**
     * 8) Should handle aprobar and rechazar dispatchers as simple event emissions
     */
    public function test_rechazar_dispatches_confirmar_rechazo_event()
    {
        $component = Livewire::test(Solicitudes::class)
            ->call('rechazar', 456);
        $component->assertDispatched('confirmarRechazo', 456);
    }

    /**
     * 9) Should do nothing when confirmadoAprobacion receives non-existing cod_sol
     */
    public function test_confirmado_aprobacion_with_non_existing_code_does_nothing()
    {
        // Ensure table empty
        $this->assertDatabaseCount('solicitante', 0);

        $component = Livewire::test(Solicitudes::class)
            ->call('confirmadoAprobacion', 999);

        // No bitacora entries, no errors thrown
        $this->assertDatabaseCount('bitacora', 0);
        $component->assertNotDispatched('swal');
    }

    /**
     * 10) Should do nothing when confirmadoRechazo receives non-existing cod_sol
     */
    public function test_confirmado_rechazo_with_non_existing_code_does_nothing()
    {
        $this->assertDatabaseCount('solicitante', 0);

        $component = Livewire::test(Solicitudes::class)
            ->call('confirmadoRechazo', 1000);

        $this->assertDatabaseCount('bitacora', 0);
        $component->assertNotDispatched('swal');
    }
}
