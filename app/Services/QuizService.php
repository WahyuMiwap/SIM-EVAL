<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Participant;
use App\Models\ParticipantAnswer;
use App\Models\Question;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuizService
{
    public function findEventByKode(string $kode): ?Event
    {
        $kode = strtoupper(trim($kode));

        return Event::with(['lokasi', 'pretestPackage.questions', 'posttestPackage.questions'])
            ->where('kode_join', $kode)->first();
    }

    public function scoreAndPersist(Participant $participant, string $type, array $answers): array
    {
        return DB::transaction(function () use ($participant, $type, $answers) {
            $correct = 0;
            $total = count($answers);
            foreach ($answers as $a) {
                $q = Question::find($a['question_id']);
                if (! $q) {
                    continue;
                }
                $isCorrect = strtoupper($a['jawaban']) === strtoupper($q->kunci);
                if ($isCorrect) {
                    $correct++;
                }
                ParticipantAnswer::updateOrCreate(
                    ['participant_id' => $participant->id, 'question_id' => $q->id, 'stage' => $type],
                    ['jawaban' => strtoupper($a['jawaban']), 'is_correct' => $isCorrect]
                );
            }
            $score = $total > 0 ? round($correct / $total * 100, 2) : 0;
            if ($type === 'pretest') {
                $participant->pretest_score = $score;
            } else {
                $participant->posttest_score = $score;
            }
            $participant->save();

            Log::info('Quiz submitted', ['participant_id' => $participant->id, 'event_id' => $participant->event_id, 'type' => $type, 'score' => $score]);

            return ['score' => $score, 'correct' => $correct, 'total' => $total];
        });
    }
}
