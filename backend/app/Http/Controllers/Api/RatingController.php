<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Formation;
use App\Models\User;
use App\Services\FormationRatingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Gère la notation des formations par les apprenants.
 */
class RatingController extends Controller
{
    /**
     * Crée une note pour la formation ciblée.
     */
    public function store(Request $request, Formation $formation, FormationRatingService $ratingService): JsonResponse
    {
        /** @var User|null $user */
        $user = auth('api')->user();

        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $validated = $request->validate([
            'note' => ['required', 'integer'],
            'commentaire' => ['nullable', 'string'],
        ]);

        $result = $ratingService->createForLearner($formation, $user, $validated);

        if (! $result['ok']) {
            return response()->json([
                'message' => $result['message'],
            ], $result['status']);
        }

        $rating = $result['rating'];

        return response()->json([
            'message' => $result['message'],
            'rating' => [
                'id' => $rating->id,
                'user_id' => $rating->user_id,
                'formation_id' => $rating->formation_id,
                'note' => $rating->note,
                'commentaire' => $rating->commentaire,
                'created_at' => $rating->created_at,
            ],
        ], $result['status']);
    }
}

