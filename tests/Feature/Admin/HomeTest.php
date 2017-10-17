<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;

class HomeTest extends TestCase
{
    protected $homeApi = '/admin/api/home';
    protected $admin;

    public function setUp()
    {
        parent::setUp();

        if (! $this->admin) {
            $this->admin = User::find(1);
        }
    }

    public function testHome()
    {
        $response = $this->actingAs($this->admin)
            ->get($this->homeApi);
        $response->assertJsonStructure(['message']);
    }
}
