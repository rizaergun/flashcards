<?php

namespace Tests\Feature;

use App\Models\Flashcard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FlashcardInteractiveTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_flashcard_create_yes()
    {
        $this->artisan('flashcard:interactive')
            ->expectsChoice('What do you want to do?', 'create', ['create', 'list', 'practice', 'stats', 'reset', 'exit'])
            ->expectsQuestion("What's the question?", "Test Question?")
            ->expectsQuestion("What's the answer?", "Test")
            ->expectsConfirmation('Do you want to save?', 'yes')
            ->expectsOutput('The flashcard was created successfully!')
            ->expectsConfirmation('Do you want to add a new flashcard?')
            ->expectsChoice('What do you want to do?', 'exit', ['create', 'list', 'practice', 'stats', 'reset', 'exit'])
            ->assertExitCode(1);
    }

    /**
     * @return void
     */
    public function test_flashcard_create_no()
    {
        $this->artisan('flashcard:interactive')
            ->expectsChoice('What do you want to do?', 'create', ['create', 'list', 'practice', 'stats', 'reset', 'exit'])
            ->expectsQuestion("What's the question?", "Test Question?")
            ->expectsQuestion("What's the answer?", "Test")
            ->expectsConfirmation('Do you want to save?', 'no')
            ->expectsOutput('The flashcard was not created!')
            ->expectsConfirmation('Do you want to add a new flashcard?')
            ->expectsChoice('What do you want to do?', 'exit', ['create', 'list', 'practice', 'stats', 'reset', 'exit'])
            ->assertExitCode(1);
    }

    /**
     * @return void
     */
    public function test_flashcard_list()
    {
        $flashcard = Flashcard::create(['question' => "Test question?", 'answer' => "test"]);

        $this->artisan('flashcard:interactive')
            ->expectsChoice('What do you want to do?', 'list', ['create', 'list', 'practice', 'stats', 'reset', 'exit'])
            ->expectsTable(['Question', 'Answer'], [
                ['Test question?', 'test']
            ])
            ->expectsChoice('What do you want to do?', 'exit', ['create', 'list', 'practice', 'stats', 'reset', 'exit'])
            ->assertExitCode(1);
    }

    /**
     * @return void
     */
    public function test_flashcard_practice_no_question()
    {
        $this->artisan('flashcard:interactive')
            ->expectsChoice('What do you want to do?', 'practice', ['create', 'list', 'practice', 'stats', 'reset', 'exit'])
            ->expectsOutputToContain('All questions answered. No questions to practice!')
            ->expectsChoice('What do you want to do?', 'exit', ['create', 'list', 'practice', 'stats', 'reset', 'exit'])
            ->assertExitCode(1);
    }

    /**
     * @return void
     */
    public function test_flashcard_practice()
    {
        $flashcard = Flashcard::create(['question' => "Test question?", 'answer' => "test"]);

        $this->artisan('flashcard:interactive')
            ->expectsChoice('What do you want to do?', 'practice', ['create', 'list', 'practice', 'stats', 'reset', 'exit'])
            ->expectsTable(['ID', 'Question', 'User Answer'], [
                ['1', 'Test question?', 'not answered']
            ])
            ->expectsQuestion('Which flashcard do you want to practice with? (ID)', '1')
            ->expectsQuestion('What is your answer to this question?', 'test')
            ->expectsOutputToContain('Congratulations! You answered the question correctly!')
            ->expectsConfirmation('Do you wish to continue?')
            ->expectsChoice('What do you want to do?', 'exit', ['create', 'list', 'practice', 'stats', 'reset', 'exit'])
            ->assertExitCode(1);
    }

    /**
     * @return void
     */
    public function test_flashcard_stats()
    {
        $flashcards = [
            ['question' => "Test question?", 'answer' => "test", 'user_answer' => "correct"],
            ['question' => "Test question 2?", 'answer' => "test2", 'user_answer' => "incorrect"],
        ];

        $flashcard = Flashcard::insert($flashcards);

        $this->artisan('flashcard:interactive')
            ->expectsChoice('What do you want to do?', 'stats', ['create', 'list', 'practice', 'stats', 'reset', 'exit'])
            ->expectsTable(['Total Questions', 'Answered', 'Correct Answer'], [
                ['2', '100%', '50%']
            ])
            ->expectsChoice('What do you want to do?', 'exit', ['create', 'list', 'practice', 'stats', 'reset', 'exit'])
            ->assertExitCode(1);
    }

    /**
     * @return void
     */
    public function test_flashcard_reset()
    {
        $this->artisan('flashcard:interactive')
            ->expectsChoice('What do you want to do?', 'reset', ['create', 'list', 'practice', 'stats', 'reset', 'exit'])
            ->expectsOutput('All question answers have been reset!')
            ->expectsChoice('What do you want to do?', 'exit', ['create', 'list', 'practice', 'stats', 'reset', 'exit'])
            ->assertExitCode(1);
    }

    /**
     * @return void
     */
    public function test_flashcard_exit()
    {
        $this->artisan('flashcard:interactive')
            ->expectsChoice('What do you want to do?', 'exit', ['create', 'list', 'practice', 'stats', 'reset', 'exit'])
            ->assertExitCode(1);
    }
}
