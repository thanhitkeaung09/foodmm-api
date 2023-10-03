<?php

namespace App\Console\Commands;

use App\Services\RemindService;
use Illuminate\Console\Command;

class PushNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send push notification to users who is planed.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(RemindService $remindService)
    {
        info('Remind to user.', []);

        $remindService->remind();

        return Command::SUCCESS;
    }
}
