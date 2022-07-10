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
        // Flashcard Menu
        choice:
        $defaultIndex = "create";
        $index = $this->choice(
            'What do you want to do?',
            ['create', 'list', 'practice', 'stats', 'reset', 'exit'],
            $defaultIndex
        );

        // Create a Flashcard
        create:
        if ($index === "create") {
            $question = $this->ask("What's the question?");
            $answer = $this->ask("What's the answer?");

            $save = $this->confirm("Do you want to save?");

            if ($save) {
                $flashcard = Flashcard::create([
                    'question' => $question,
                    'answer' => $answer
                ]);

                $this->info("The flashcard was created successfully!");
            } else {
                $this->error("The flashcard was not created!");
            }

            $new = $this->confirm("Do you want to add a new flashcard?");

            if ($new) {
                goto create;
            }
        }

        // List All Flashcards
        if ($index === "list") {
            $this->table(
                ['Question', 'Answer'],
                Flashcard::all(['question', 'answer'])->toArray()
            );
        }

        // Practice
        practice:
        if ($index === "practice") {
            $flashcards = Flashcard::all(['id', 'question', 'user_answer']);
            $count_of_answered = Flashcard::whereIn('user_answer', ['correct', 'incorrect']);

            // all flashcards to practice
            $this->table(
                ["ID", "Question", "User Answer"],
                $flashcards->toArray()
            );

            // progress bar
            $bar = $this->output->createProgressBar($flashcards->count());
            $bar->advance($count_of_answered->count());
            $this->newLine(2);

            if ($flashcards->count() === $count_of_answered->count()) {
                $this->warn('All questions answered. No questions to practice!');
                goto choice;
            }

            $id = $this->ask('Which flashcard do you want to practice with? (ID)');

            $flashcard = Flashcard::whereId($id)
                ->whereIn('user_answer', ['not answered', 'incorrect'])
                ->first();

            if ($flashcard) {
                $this->line('Question: ' . $flashcard->question);
                $user_answer = $this->ask('What is your answer to this question?');

                if ($flashcard->answer === $user_answer) {
                    $flashcard->user_answer = "correct";
                    $flashcard->save();
                    $this->info('Congratulations! You answered the question correctly!');
                } else {
                    $flashcard->user_answer = "incorrect";
                    $flashcard->save();
                    $this->error('Sorry! Your answer was not correct!');
                }
            } else {
                $this->warn('Sorry! We could not find the flashcard or you have already answered!');
            }

            if ($this->confirm('Do you wish to continue?')) {
                goto practice;
            }
        }

        // Stats
        if ($index === "stats") {
            $total_question = Flashcard::all()->count();
            $percent_of_answered = 0 . '%';
            $percent_of_correct_answers = 0 . '%';

            if ($total_question) {
                $answered = Flashcard::whereIn('user_answer', ['incorrect', 'correct'])->get()->count();
                $percent_of_answered = round(($answered / $total_question) * 100, 2) . '%';

                $correct_answer = Flashcard::whereUserAnswer('correct')->get()->count();
                $percent_of_correct_answers = round(($correct_answer / $total_question) * 100, 2) . '%';
            }

            $this->table(['Total Questions', 'Answered', 'Correct Answer'],
                [
                    [
                        $total_question,
                        $percent_of_answered,
                        $percent_of_correct_answers]
                ]);
        }

        // Reset
        if ($index === "reset") {
            Flashcard::query()->update(['user_answer' => 'not answered']);
            $this->info('All question answers have been reset!');
        }

        // Exit
        if ($index === "exit") {
            return 1;
        }

        goto choice;
    }
}
