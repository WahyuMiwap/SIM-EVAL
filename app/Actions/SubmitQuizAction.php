<?php

namespace App\Actions;

use App\Models\Participant;
use App\Services\QuizService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubmitQuizAction
{
    public function __construct(private QuizService $quiz) {}

    public function handle(Participant $participant, string $type, array $answers): array
    {
        return DB::transaction(function () use ($participant, $type, $answers) {
            $result = $this->quiz->scoreAndPersist($participant, $type, $answers);
            Log::info('Quiz submit (action)', ['participant_id' => $participant->id, 'type' => $type, 'score' => $result['score']]);

            return $result;
        });
    }
}
