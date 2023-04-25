<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ArticleDeleteModal extends Component
{
    public $article;
    public $showDeleteModal = false;
    protected $listeners = ['confirmArticleDeletion'];
    public function render()
    {
        return view('livewire.article-delete-modal');
    }
    public function confirmArticleDeletion($article)
    {
        if($this->article->id == $article['id'])
        {
            $this->showDeleteModal = true;
        }
    }
    public function delete()
    {
        Storage::disk('public')->delete($this->article->image);
        $this->article->delete();
        session()->flash('flash.banner', __('Article deleted successfully'));
        session()->flash('flash.bannerStyle', 'danger');
        $this->redirect(route('articles.index'));
    }
}
