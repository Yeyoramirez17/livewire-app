<?php

namespace Tests\Feature\Livewire;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ArticleFormTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @test
     */
    function article_form_renders_properly()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('articles.create'))
                ->assertSeeLivewire('article-form')
                ->assertDontSeeText(__('Delete'));

        $article = Article::factory()->create();

        $this->actingAs($user)->get(route('articles.edit', $article))
            ->assertSeeLivewire('article-form')
            ->assertSeeText(__('Delete'));
    }
    /**
     * @test
     */
    public function blade_template_is_wired_properly()
    {
        Livewire::test('article-form')
            ->assertSeeHtml('wire:submit.prevent="save"')
            ->assertSeeHtml('wire:model="article.title"')
            ->assertSeeHtml('wire:model="article.slug"');
    }
    /**
     * @test
     */
    function can_create_new_article()
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $image = UploadedFile::fake()->image('post-image.png');
        $category = Category::factory()->create();

        Livewire::actingAs($user)->test('article-form')
            ->set('article.title', 'New Article Title')
            ->set('image', $image)
            ->set('article.slug', 'new-article')
            ->set('article.content', 'New Article Content')
            ->set('article.category_id', $category->id)
            ->call('save')
            ->assertRedirect(route('articles.index'));

        $this->assertDatabaseHas('articles', [
            'title' => 'New Article Title',
            'image' => $imagePath = Storage::disk('public')->files()[0],
            'slug' => 'new-article',
            'content' => 'New Article Content',
            'category_id' => $category->id,
            'user_id' => $user->id
        ]);

        Storage::disk('public')->assertExists($imagePath);
    }
    /**
     * @test
     */
    function can_update_article()
    {
        $article = Article::factory()->create([
            'image' => '/path/image/'
        ]);
        $user = User::factory()->create();

        Livewire::actingAs($user)->test('article-form', ['article' => $article])
            ->assertSet('article.title', $article->title)
            ->assertSet('article.slug', $article->slug)
            ->assertSet('article.content', $article->content)
            ->assertSee('article.category_id', $article->category->id)
            ->set('article.title', 'Updated title')
            ->set('article.slug', 'updated-slug')
            ->call('save')
            ->assertRedirect(route('articles.index'));


        $this->assertDatabaseCount('articles', 1);

        $this->assertDatabaseHas('articles', [
            'title' => 'Updated title',
            'slug' => 'updated-slug',
            'user_id' => $user->id
        ]);
    }
    /**
     * @test
     */
    function can_update_article_image()
    {
        Storage::fake('public');
        $oldImage = UploadedFile::fake()->image('old-image.png');
        $oldImagePath = $oldImage->store('/', 'public');

        $newImage = UploadedFile::fake()->image('new-image.png');

        $article = Article::factory()->create([
            'image' => $oldImagePath
        ]);

        $user = User::factory()->create();

        Livewire::actingAs($user)->test('article-form', ['article' => $article])
            ->set('image', $newImage)
            ->call('save')
            ->assertRedirect(route('articles.index'));

        Storage::disk('public')
            ->assertExists($article->fresh()->image)
            ->assertMissing($oldImage);
    }
    /**
     * @test
     */
    function title_is_required()
    {
        Livewire::test('article-form')
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasErrors(['article.title' => 'required']);
    }
    /**
     * @test
     */
    function image_is_required()
    {
        Livewire::test('article-form')
        ->set('article.title', 'Article title')
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasErrors(['image' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'image']));
    }
    /**
     * @test
     */
    function image_field_must_be_of_type_image()
    {
        Livewire::test('article-form')
            ->set('image', 'string-not-allowed')
            ->call('save')
            ->assertHasErrors(['image' => 'image'])
            ->assertSeeHtml(__('validation.image', ['attribute' => 'image']));
    }
    /**
     * @test
     */
    function image_must_be_2mb_max()
    {
        Storage::fake('public');
        $image = UploadedFile::fake()->image('post-image.png')->size(3000);

        Livewire::test('article-form')
            ->set('image', $image)
            ->call('save')
            ->assertHasErrors(['image' => 'max'])
            ->assertSeeHtml(__('validation.max.file', [
                'attribute' => 'image',
                'max' => '2048'
        ]));
    }
    /**
     * @test
     */
    function slug_is_required()
    {
        Livewire::test('article-form')
            ->set('article.title', 'Article Title')
            ->set('article.slug', null)
            ->set('article.content', 'Article Content')
            ->call('save')
            ->assertHasErrors(['article.slug' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'slug']));
    }
     /**
     * @test
     */
    function category_is_required()
    {
        Livewire::test('article-form')
            ->set('article.title', 'Article Title')
            ->set('article.slug', 'article-title')
            ->set('article.content', 'Article Content')
            ->set('article.category_id', null)
            ->call('save')
            ->assertHasErrors(['article.category_id' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'category id']));
    }
    /** @test */
    function category_must_exist_in_database()
    {
        Livewire::test('article-form')
            ->set('article.title', 'Article Title')
            ->set('article.slug', 'article-title')
            ->set('article.content', 'Article Content')
            ->set('article.category_id', 1)
            ->call('save')
            ->assertHasErrors(['article.category_id' => 'exists'])
            ->assertSeeHtml(__('validation.exists', ['attribute' => 'category id']));
    }
    /** @test */
    public function can_create_new_category()
    {
        Livewire::test('article-form')
            ->call('openCategoryModal')
            ->set('newCategory.name', 'Laravel')
            ->assertSee('newCategory.slug', 'laravel')
            ->call('saveNewCategory')
            ->assertSee('article.category_id', Category::first()->id)
            ->assertSee('showCategoryModal', false);
    }
    /** @test */
    public function new_category_name_is_required()
    {
        Livewire::test('article-form')
            ->call('openCategoryModal')
            ->assertSee('newCategory.slug', 'laravel')
            ->call('saveNewCategory')
            ->assertHasErrors(['newCategory.name' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'name']));
    }
    /** @test */
    public function new_category_name_must_be_unique()
    {
        $category = Category::factory()->create();

        Livewire::test('article-form')
            ->call('openCategoryModal')
            ->set('newCategory.name', $category->name)
            ->set('newCategory.slug', 'laravel')
            ->call('saveNewCategory')
            ->assertHasErrors(['newCategory.name' => 'unique'])
            ->assertSeeHtml(__('validation.unique', ['attribute' => 'name']));
    }
    /** @test */
    public function new_category_slug_must_be_unique()
    {
        $category = Category::factory()->create();

        Livewire::test('article-form')
            ->call('openCategoryModal')
            ->set('newCategory.name', 'Laravel')
            ->set('newCategory.slug', $category->slug)
            ->call('saveNewCategory')
            ->assertHasErrors(['newCategory.slug' => 'unique'])
            ->assertSeeHtml(__('validation.unique', ['attribute' => 'slug']));
    }
    /** @test */
    public function new_category_slug_is_required()
    {
        Livewire::test('article-form')
            ->call('openCategoryModal')
            ->set('newCategory.name', 'Laravel')
            ->set('newCategory.slug', null)
            ->call('saveNewCategory')
            ->assertHasErrors(['newCategory.slug' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'slug']));
    }
    /**
     * @test
     */
    function slug_must_be_unique()
    {
        $article = Article::factory()->create();

        Livewire::test('article-form')
            ->set('article.title', 'Article Title')
            ->set('article.slug', $article->slug)
            ->set('article.content', 'Article Content')
            ->call('save')
            ->assertHasErrors(['article.slug' => 'unique']);
    }
    /**
     * @test
     */
    public function slug_must_only_contain_letters_numbers_dashes_and_underscores()
    {
        Livewire::test('article-form')
            ->set('article.title', 'New Article')
            ->set('article.slug', 'new_article$%^')
            ->set('article.content', 'Article Content')
            ->call('save')
            ->assertHasErrors(['article.slug' => 'alpha_dash'])
            ->assertSeeHtml(__('validation.alpha_dash', ['attribute' => 'slug']));
    }
    /**
     * @test
     */
    public function title_must_be_4_characters_min()
    {
        Livewire::test('article-form')
            ->set('article.title', 'Art')
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasErrors(['article.title' => 'min']);
    }
    /**
     * @test
     */
    public function unique_rule_should_be_ignored_when_updating_the_same_slug()
    {
        $article = Article::factory()->create();
        $user = User::factory()->create();

        Livewire::actingAs($user)->test('article-form', ['article' => $article])
            ->set('article.title', 'New Article')
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasNoErrors(['article.title' => 'min']);
    }
    /**
     * @test
     */
    public function content_is_required()
    {
        Livewire::test('article-form')
            ->set('article.title', 'Article title')
            ->call('save')
            ->assertHasErrors(['article.content' => 'required']);
    }
    /**
     * @test
     */
    public function real_time_validation_works_for_title()
    {
        Livewire::test('article-form')
            ->set('article.title', '')
            ->assertHasErrors(['article.title' => 'required'])
            ->set('article.title', 'Art')
            ->assertHasErrors(['article.title' => 'min'])
            ->set('article.title', 'Article Title')
            ->assertHasNoErrors('article.title');
    }
    /**
     * @test
     */
    public function real_time_validation_works_for_content()
    {
        Livewire::test('article-form')
            ->set('article.content', '')
            ->assertHasErrors(['article.content' => 'required'])
            ->set('article.content', 'Article Content')
            ->assertHasNoErrors('article.content');
    }
    /**
     * @test
     */
    public function slug_is_generated_automatically()
    {
        Livewire::test('article-form')
            ->set('article.title', 'Nuevo articulo')
            ->assertSet('article.slug', 'nuevo-articulo');
    }
    /**
     * @test
     */
    public function guest_cannot_create_or_update_articles()
    {
        $this->get(route('articles.create'))
            ->assertRedirect('login');

        $article = Article::factory()->create();

        $this->get(route('articles.edit', $article))
            ->assertRedirect('login');
    }

}
