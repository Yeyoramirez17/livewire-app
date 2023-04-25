<?php

namespace Tests\Feature\Livewire;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ArticleDeleteModalTest extends TestCase
{
    use LazilyRefreshDatabase;
    /** @test */
    function can_delete_article()
    {
        Storage::fake();

        $imagePath = UploadedFile::fake()
            ->image('image.png')
            ->store('/', 'public');

        $article = Article::factory()->create([
            'image' => $imagePath
        ] );
        $user = User::factory()->create();

        Livewire::actingAs($user)->test('article-delete-modal', ['article' => $article])
            ->call('delete')
            ->assertSessionHas('status')
            ->assertRedirect(route('articles.index'));

        Storage::disk('public')->assertMissing($imagePath);

        $this->assertDatabaseCount('articles', 0);
    }
}
