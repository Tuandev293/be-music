<?php

namespace App\Jobs;

use App\Models\LogListen;
use App\Models\Song;
use Carbon\Carbon;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class WriteLogListen implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $path;
    protected $clientIp;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($clientIp, $path)
    {
        $this->clientIp = $clientIp;
        $this->path = $path;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $song = Song::where('file_path', $this->path)->first();
            if(empty($song)) return;
            $today = Carbon::now()->format('Y-m-d');
            LogListen::firstOrCreate([
                'song_id' => $song->id,
                'ip' => $this->clientIp,
                'date_listen' => $today
            ]);
        } catch (Exception $e) {
            Log::error("[UserController][login] error because" . $e->getMessage());
        }

    }
}
