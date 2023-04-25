<?php

namespace App\Http\Livewire;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;


class ArticleForm extends Component
{
    use WithFileUploads;
    public Article $article;
    public $newCategory;
    public $image;
    public $showCategoryModal = false;
    public function openCategoryModal()
    {
        $this->newCategory = new Category();
        $this->showCategoryModal = true;
    }
    public function closeCategoryModal()
    {
        $this->showCategoryModal = false;
        $this->newCategory = null;
        $this->clearValidation('newCategory.*');
    }
    public function saveNewCategory()
    {
        $this->validateOnly('newCategory.name');
        $this->validateOnly('newCategory.slug');
        $this->newCategory->save();
        $this->article->category_id = $this->newCategory->id;
        $this->closeCategoryModal();
    }
    public function rules()
    {
        return [
            'image' => [
                Rule::requiredIf(!$this->article->image),
                Rule::when($this->image, ['image', 'max:2048'])
            ],
            'article.title' => ['required', 'min:4'],
            'article.slug' => [
                'required',
                'alpha_dash',
                Rule::unique('articles', 'slug')->ignore($this->article)
            ],
            'article.content' => ['required'],
            'article.category_id' => [
                'required',
                Rule::exists('categories', 'id')
            ],
            'newCategory.name' => [
                Rule::requiredIf($this->newCategory instanceof Category),
                Rule::unique('categories', 'name')
            ],
            'newCategory.slug' => [
                Rule::requiredIf($this->newCategory instanceof Category),
                Rule::unique('categories', 'slug')
            ]
        ];
    }
    public function mount(Article $article)
    {
        $this->article = $article;
    }
    public function save()
    {
        $this->validate();

        if ($this->image) {

            $this->article->image = $this->uploadImage();
        }

        Auth::user()->articles()->save($this->article);

        session()->flash('flash.banner', __('Article saved successfully'));

        $this->redirectRoute('articles.index');
    }
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    public function updatedArticleTitle($title)
    {
        $this->article->slug = Str::slug($title);
    }
    public function updatedNewCategoryName($name)
    {
        $this->newCategory->slug = Str::slug($name);
    }
    protected function uploadImage()
    {
        if ($oldImage = $this->article->image) {
            Storage::disk('public')->delete($oldImage);
        }
        return $this->image->store('/', 'public');
    }
    public function render()
    {
        return view('livewire.article-form', [
            'categories' => Category::pluck('name', 'id')
        ]);
    }
}
