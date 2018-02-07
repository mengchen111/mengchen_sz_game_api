<?php

namespace App\Console\Commands;

use App\Models\ServerRoomsHistory;
use Illuminate\Console\Command;

class testCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:test {ruid}';

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
        $ruid = $this->argument('ruid');
        $record = ServerRoomsHistory::with('recordInfo')->where('ruid', $ruid)->first();
        if (!empty($record->recordInfo)) {
            return $this->info($record->recordInfo->id);
        }
        return $this->info('not fund');
    }
}
