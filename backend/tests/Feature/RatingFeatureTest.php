<?php

namespace Tests\Feature;

use App\Models\Enrollment;
use App\Models\Formation;
use App\Models\Rating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class RatingFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrolled_learner_can_rate_a_formation(): void
    {
        [$formation, $learner] = $this->createFormationWithEnrolledLearner();

        $response = $this->postJson(
            "/api/formations/{$formation->id}/noter",
            ['note' => 4, 'commentaire' => 'Tres bonne formation'],
            $this->authHeadersFor($learner)
        );

        $response->assertStatus(201)
            ->assertJsonPath('rating.user_id', $learner->id)
            ->assertJsonPath('rating.formation_id', $formation->id)
            ->assertJsonPath('rating.note', 4);

        $this->assertDatabaseHas('ratings', [
            'user_id' => $learner->id,
            'formation_id' => $formation->id,
            'note' => 4,
        ]);
    }

    public function test_learner_cannot_rate_same_formation_twice(): void
    {
        [$formation, $learner] = $this->createFormationWithEnrolledLearner();

        Rating::query()->create([
            'user_id' => $learner->id,
            'formation_id' => $formation->id,
            'note' => 5,
            'commentaire' => 'Premier avis',
        ]);

        $response = $this->postJson(
            "/api/formations/{$formation->id}/noter",
            ['note' => 4, 'commentaire' => 'Second avis'],
            $this->authHeadersFor($learner)
        );

        $response->assertStatus(400)
            ->assertJsonStructure(['message']);
    }

    public function test_out_of_range_note_returns_bad_request(): void
    {
        [$formation, $learner] = $this->createFormationWithEnrolledLearner();

        $response = $this->postJson(
            "/api/formations/{$formation->id}/noter",
            ['note' => 6, 'commentaire' => 'Impossible'],
            $this->authHeadersFor($learner)
        );

        $response->assertStatus(400)
            ->assertJsonStructure(['message']);
    }

    public function test_not_enrolled_learner_gets_forbidden(): void
    {
        $formation = $this->createFormation();
        $learner = User::factory()->create(['role' => 'apprenant']);

        $response = $this->postJson(
            "/api/formations/{$formation->id}/noter",
            ['note' => 4],
            $this->authHeadersFor($learner)
        );

        $response->assertStatus(403)
            ->assertJsonStructure(['message']);
    }

    public function test_rating_requires_jwt_token(): void
    {
        $formation = $this->createFormation();

        $response = $this->postJson("/api/formations/{$formation->id}/noter", ['note' => 4]);

        $response->assertStatus(401);
    }

    public function test_formation_details_include_rating_aggregates(): void
    {
        [$formation, $learnerOne] = $this->createFormationWithEnrolledLearner();
        $learnerTwo = User::factory()->create(['role' => 'apprenant']);

        Enrollment::query()->create([
            'utilisateur_id' => $learnerTwo->id,
            'formation_id' => $formation->id,
            'progression' => 0,
        ]);

        Rating::query()->create([
            'user_id' => $learnerOne->id,
            'formation_id' => $formation->id,
            'note' => 4,
            'commentaire' => 'Bien',
        ]);

        Rating::query()->create([
            'user_id' => $learnerTwo->id,
            'formation_id' => $formation->id,
            'note' => 5,
            'commentaire' => 'Excellent',
        ]);

        $response = $this->getJson("/api/formations/{$formation->id}");

        $response->assertOk()
            ->assertJsonPath('formation.nombre_avis', 2)
            ->assertJsonPath('formation.note_moyenne', 4.5);
    }

    /**
     * @return array{0:Formation,1:User}
     */
    private function createFormationWithEnrolledLearner(): array
    {
        $formation = $this->createFormation();
        $learner = User::factory()->create(['role' => 'apprenant']);

        Enrollment::query()->create([
            'utilisateur_id' => $learner->id,
            'formation_id' => $formation->id,
            'progression' => 0,
        ]);

        return [$formation, $learner];
    }

    private function createFormation(): Formation
    {
        $trainer = User::factory()->create(['role' => 'formateur']);

        return Formation::query()->create([
            'titre' => 'API Laravel',
            'description' => 'Formation backend complete',
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


