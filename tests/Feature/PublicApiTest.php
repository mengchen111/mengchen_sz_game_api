<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PublicApiTest extends TestCase
{
    protected $infoApi = '/api/info';
    protected $admin;

    public function setUp()
    {
        parent::setUp();

        if (! $this->admin) {
            $this->admin = User::find(1);
        }
    }

    public function testInfoApi()
    {
        $response = $this->actingAs($this->admin)
            ->get($this->infoApi);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'account',
                'created_at',
                'group',
                'group_id',
                'id',
                'name',
                'parent',
                'parent_id',
                'updated_at'
            ]);
    }
}
