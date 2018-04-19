<?php

namespace App\Console\Commands;

use App\Console\BaseCommand;
use Illuminate\Http\Request;

class TestCallController extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:call-route';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '测试命令行调用路由';

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
        $uri = '/players/find';
        $params = [
            'uid' => 10000,
        ];
        $request = Request::create($uri, 'POST', $params);
        $response = app()->make(\Illuminate\Contracts\Http\Kernel::class)->handle($request);
        $result = $response->getContent();
        return $this->logInfo($result);
    }
}
