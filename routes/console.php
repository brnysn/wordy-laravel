<?php

use Illuminate\Support\Facades\Artisan;
use App\Models\Question;

Artisan::command('seed-questions', function () {
    $this->comment('Seeding questions...');

    $alphabet = \App\Models\Alphabet::all();    
    $count = 0;    
    for ($i = 0; $i < 300; $i++) {
        $api_url = 'https://api.radkod.com/parolla/api/v1/modes/unlimited/';

        $data = \Http::get($api_url)->json()['data']['questions'];
        foreach ($data as $question) {
            if (Question::where('id', $question['id'])->exists()) {
                info("Question {$question['id']} already exists, skipping...");
                continue;
            }

            $alphabet_id = $alphabet->where('name', $question['letter'])->first()->id;
            Question::create([
                'id' => $question['id'],
                'alphabet_id' => $alphabet_id,
                'question' => $question['question'],
                'answer' => $question['answer'],
                'release_at' => now(),
                'level' => 2,
            ]);
            $count++;
        }
        @sleep(1);
    }

    $this->info("Seeded {$count} questions.");
});
