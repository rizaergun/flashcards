<?php

namespace App\Console\Commands;

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
    }
}
