<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ViewTest extends TestCase
{
    protected $homeApi = '/admin/home';
    protected $homeView = 'admin.home';
    protected $systemLogApi = '/admin/system/log';
    protected $systemLogView = 'admin.system.log';
    protected $admin;
    protected $noneAdmin;

    public function setUp()
    {
        parent::setUp();

        if (! $this->admin) {
            $this->admin = User::find(1);
        }

        if (! $this->noneAdmin) {
            $this->noneAdmin = factory(User::class)->create();
        }
    }

    public function testHome()
    {
        $res = $this->actingAs($this->admin)
            ->get($this->homeApi);
        $res->assertStatus(200);
        $res->assertViewIs($this->homeView);

        //非管理员访问返回403
        $res = $this->actingAs($this->noneAdmin)
            ->get($this->homeApi);
        $res->assertStatus(403);
    }

    public function testSystemLog()
    {
        $res = $this->actingAs($this->admin)
            ->get($this->systemLogApi);
        $res->assertStatus(200);
        $res->assertViewIs($this->systemLogView);

        //非管理员访问返回403
        $res = $this->actingAs($this->noneAdmin)
            ->get($this->systemLogApi);
        $res->assertStatus(403);
    }
}
