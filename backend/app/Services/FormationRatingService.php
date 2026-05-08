<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Formation;
use App\Models\Rating;
use App\Models\User;

/**
 * Encapsule la logique métier de notation des formations.
 */
class FormationRatingService
{
    /**
     * @param  array{note:mixed,commentaire:mixed}  $payload
     * @return array{ok:bool,status:int,message:string,rating?:Rating}
     */
    public function createForLearner(Formation $formation, User $user, array $payload): array
    {
        $note = (int) ($payload['note'] ?? 0);
        $commentaire = isset($payload['commentaire']) ? trim((string) $payload['commentaire']) : null;
        $result = [];

        if ($note < 1 || $note > 5) {
            $result = [
                'ok' => false,
                'status' => 400,
                'message' => 'La note doit être comprise entre 1 et 5.',
            ];
        } else {
            $isEnrolled = Enrollment::query()
                ->where('utilisateur_id', $user->id)
                ->where('formation_id', $formation->id)
                ->exists();

            if (! $isEnrolled) {
                $result = [
                    'ok' => false,
                    'status' => 403,
                    'message' => 'Vous devez être inscrit à cette formation pour la noter.',
                ];
            } else {
                $alreadyRated = Rating::query()
                    ->where('user_id', $user->id)
                    ->where('formation_id', $formation->id)
                    ->exists();

                if ($alreadyRated) {
                    $result = [
                        'ok' => false,
                        'status' => 400,
                        'message' => 'Vous avez déjà noté cette formation.',
                    ];
                } else {
                    $rating = Rating::query()->create([
                        'user_id' => $user->id,
                        'formation_id' => $formation->id,
                        'note' => $note,
                        'commentaire' => $commentaire === '' ? null : $commentaire,
                    ]);

                    $result = [
                        'ok' => true,
                        'status' => 201,
                        'message' => 'Rating created successfully',
                        'rating' => $rating,
                    ];
                }
            }
        }

        return $result;
    }
}

