<?php

namespace App\Console\Commands;

use App\Models\ServerRoomsHistory;
use Illuminate\Console\Command;

class TestServerRoomHistoryRuid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:test-server_room_history_ruid {ruid}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试record_info_new ruid bigint的relationship';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $ruid =  $this->argument('ruid');
        $this->info('intpu ruid is: ' .  $ruid);
        $record = ServerRoomsHistory::with('recordInfo')->where('ruid', 'like', $ruid)->first();
        if (empty($record)) {
            return $this->info('! server rooms history not fund');
        }

        $this->info('server rooms history id: ' . $record->id);
        if (!empty($record->recordInfo)) {
            return $this->info('record id: '. $record->recordInfo->id);
        } else {
            return $this->info('! record not fund');
        }
    }
}
