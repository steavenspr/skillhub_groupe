<?php

namespace Tests\Feature;

use App\Models\Enrollment;
use App\Models\Formation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class FormationApprenantsFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_trainer_gets_learners_list_with_expected_structure(): void
    {
        $trainer = User::factory()->create(['role' => 'formateur']);
        $formation = $this->createFormationForTrainer($trainer);
        $learner = User::factory()->create(['role' => 'apprenant']);

        Enrollment::query()->create([
            'utilisateur_id' => $learner->id,
            'formation_id' => $formation->id,
            'progression' => 60,
        ]);

        $response = $this->getJson(
            "/api/formations/{$formation->id}/apprenants",
            $this->authHeadersFor($trainer)
        );

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure([
                'data' => [[
                    'id',
                    'nom',
                    'email',
                    'progressions',
                    'date_inscription',
                ]],
            ])
            ->assertJsonPath('data.0.id', $learner->id)
            ->assertJsonPath('data.0.email', $learner->email)
            ->assertJsonPath('data.0.progressions', 60);
    }

    public function test_non_owner_trainer_gets_forbidden(): void
    {
        $owner = User::factory()->create(['role' => 'formateur']);
        $otherTrainer = User::factory()->create(['role' => 'formateur']);
        $formation = $this->createFormationForTrainer($owner);

        $response = $this->getJson(
            "/api/formations/{$formation->id}/apprenants",
            $this->authHeadersFor($otherTrainer)
        );

        $response->assertStatus(403);
    }

    public function test_owner_trainer_gets_empty_array_when_no_learners_enrolled(): void
    {
        $trainer = User::factory()->create(['role' => 'formateur']);
        $formation = $this->createFormationForTrainer($trainer);

        $response = $this->getJson(
            "/api/formations/{$formation->id}/apprenants",
            $this->authHeadersFor($trainer)
        );

        $response->assertOk()
            ->assertJson([
                'data' => [],
            ]);
    }

    public function test_request_without_jwt_token_gets_unauthorized(): void
    {
        $trainer = User::factory()->create(['role' => 'formateur']);
        $formation = $this->createFormationForTrainer($trainer);

        $response = $this->getJson("/api/formations/{$formation->id}/apprenants");

        $response->assertStatus(401);
    }

    private function createFormationForTrainer(User $trainer): Formation
    {
        return Formation::query()->create([
            'titre' => 'Formation API Avancee',
            'description' => 'Contenu backend',
            'categorie' => 'DevOps',
            'niveau' => 'Intermédiaire',
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
