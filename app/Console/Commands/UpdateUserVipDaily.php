<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateUserVipDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:update_date_vip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update vip when end date';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');
        User::where('date_end_vip', $today)->update([
            'date_end_vip'=> null,
            'date_start_vip' => null
        ]);
        return Command::SUCCESS;
    }
}
