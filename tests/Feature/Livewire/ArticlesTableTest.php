<?php

namespace Tests\Feature\Livewire;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticlesTest extends TestCase
{
    /**
     * @test
     */
    public function articles_component_renders_properly()
    {
        $user = User::factory()->create();
        
        $this->actingAs($user)
            ->get(route('articles.index'))
            ->assertSeeLivewire('articles-table');
    }
}
