<?php

namespace App\Console\Commands;

use App\Models\Flashcard;
use Illuminate\Console\Command;

class FlashcardInteractive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flashcard:interactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flashcard Interactive';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $defaultIndex = "create";

        $index = $this->choice(
            'What do you want to do?',
            ['create', 'list', 'practice', 'stats', 'reset', 'exit'],
            $defaultIndex
        );

        // Create a Flashcard
        if ($index === "create") {
            $question = $this->ask("What's the question?");
            $answer = $this->ask("What's the answer?");

            if ($this->confirm("Do you wish to continue?")) {
                $flashcard = Flashcard::create([
                    'question' => $question,
                    'answer' => $answer
                ]);

                $this->info("The flashcard was created successfully!");
            } else {
                $this->error("The flashcard was not created!");
            }
        }

        // List All Flashcards
        if ($index === "list") {
            $this->table(
                ['Question', 'Answer'],
                FlashCard::all(['question', 'answer'])->toArray()
            );
        }
    }
}
