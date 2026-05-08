<?php

namespace Tests\Feature;

use App\Models\Formation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class FormationCatalogFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_can_filter_by_search_category_and_level(): void
    {
        $trainer = User::factory()->create(['role' => 'formateur']);

        Formation::query()->create([
            'titre' => 'Laravel API',
            'description' => 'Formation backend API',
            'categorie' => 'DevOps',
            'niveau' => 'Débutant',
            'formateur_id' => $trainer->id,
            'nombre_de_vues' => 0,
        ]);

        Formation::query()->create([
            'titre' => 'Design UI',
            'description' => 'Formation design',
            'categorie' => 'Design',
            'niveau' => 'Avancé',
            'formateur_id' => $trainer->id,
            'nombre_de_vues' => 0,
        ]);

        $response = $this->getJson('/api/formations?search=Laravel&categorie=DevOps&niveau=Débutant');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.titre', 'Laravel API');
    }

    public function test_show_increments_views_for_guest_request(): void
    {
        $formation = $this->createFormation();

        $this->getJson("/api/formations/{$formation->id}")
            ->assertOk();

        $this->assertSame(1, $formation->fresh()->nombre_de_vues);
    }

    public function test_show_does_not_increment_views_for_owner_trainer(): void
    {
        $trainer = User::factory()->create(['role' => 'formateur']);
        $formation = $this->createFormation($trainer);

        $this->getJson(
            "/api/formations/{$formation->id}",
            $this->authHeadersFor($trainer)
        )->assertOk();

        $this->assertSame(0, $formation->fresh()->nombre_de_vues);
    }

    private function createFormation(?User $trainer = null): Formation
    {
        $trainer ??= User::factory()->create(['role' => 'formateur']);

        return Formation::query()->create([
            'titre' => 'Formation API',
            'description' => 'Description de test',
            'categorie' => 'DevOps',
            'niveau' => 'Débutant',
            'formateur_id' => $trainer->id,
            'nombre_de_vues' => 0,
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function authHeadersFor(User $user): array
    {
        $token = JWTAuth::fromUser($user);

        return [
            'Authorization' => 'Bearer '.$token,
        ];
    }
}
