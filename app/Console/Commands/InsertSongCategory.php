<?php

namespace App\Console\Commands;

use App\Models\SongCategory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class InsertSongCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:insert_song_category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'insert_song_category';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        $data = [
            [
                "title"=> 'Không Xác Định',
                "slug" => 'khong-xac-dinh',
                'created_at' => $now
            ],
            [
                "title"=> 'Nhạc Trẻ',
                "slug" => 'nhac-tre',
                'created_at' => $now
            ],
            [
                "title"=> 'Nhạc Trữ Tình',
                "slug" => 'tru-tinh',
                'created_at' => $now
            ],
            [
                "title"=> 'Nhạc Pop',
                "slug" => 'pop',
                'created_at' => $now
            ],
            [
                "title"=> 'Nhạc Kháng Chiến',
                "slug" => 'nhac-khang-chien',
                'created_at' => $now
            ],
            [
                "title"=> 'Nhạc Thiếu Nhi',
                "slug" => 'nhac-thieu-nhi',
                'created_at' => $now
            ],
            [
                "title"=> 'Nhạc US-UK',
                "slug" => 'nhac-us-uk',
                'created_at' => $now
            ],
            [
                "title"=> 'Nhạc Remix',
                "slug" => 'nhac-remix',
                'created_at' => $now
            ]
        ];
        SongCategory::insert($data);
        return Command::SUCCESS;
    }
}
