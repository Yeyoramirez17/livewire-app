<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('New Article') }}</h2>
    </x-slot>

    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <x-form-section submit="save">
                @csrf
                <x-slot name="title">
                    {{ __('New Article') }}
                </x-slot>
                <x-slot name="description">
                    {{ __('Some description') }}
                </x-slot>

                <x-slot name="form">
                    <div class="col-span-6 sm:col-span-4">
                        <x-select-image wire:model="image" :image="$image" :existing="$article->image" />
                        <x-input-error for="image" class="mt-2" />
                    </div>

                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="title" :value="__('Title')" />
                        <x-input wire:model="article.title" id="title" type="text" class="mt-1 block w-full" />
                        <x-input-error for="article.title" class="mt-2" />
                    </div>

                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="slug" :value="__('Slug')" />
                        <x-input wire:model="article.slug" id="slug" type="text" class="mt-1 block w-full" />
                        <x-input-error for="article.slug" class="mt-2" />
                    </div>

                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="category_id" :value="__('Category')" />
                        <div class="flex space-x-2 mt-1">
                            <x-select wire:model="article.category_id" id="category_id" :options="$categories"
                                :placeholder="__('Select Category')" class="block w-full" />
                            <x-secondary-button wire:click="openCategoryModal" class="!p-2.5">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </x-secondary-button>
                        </div>
                        <x-input-error for="article.category_id" class="mt-2" />
                    </div>

                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="content" :value="__('Content')" />
                        <x-html-editor wire:model="article.content" id="content" type="text" row="10"
                            class="mt-1 block w-full"></x-html-editor>
                        <x-input-error for="article.content" class="mt-2" />
                        </label>
                    </div>

                </x-slot>

                <x-slot name="actions">
                    <a
                        class="inline-flex items-center px-4 py-2 bg-white border
                        border-gray-300 rounded-md font-semibold text-xs
                        text-gray-700 uppercase tracking-widest shadow-sm
                        hover:bg-gray-50 focus:outline-none focus:ring-2
                        focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25
                        transition ease-in-out duration-150 mr-auto"
                        href="{{ route('articles.index') }}">Volver</a>
                    @if ($this->article->exists)
                        <livewire:article-delete-modal :article="$article">
                            <x-danger-button wire:click="$emit('confirmArticleDeletion', {{ $article }})" class="mr-2">{{ __('Delete') }}</x-danger-button>
                        </livewire:article-delete-modal>
                    @endif
                    <x-button>{{ __('Save') }}</x-button>
                </x-slot>
                </x-jet-form-section>
        </div>
    </div>

    <x-modal wire:model="showCategoryModal">
        <form wire:submit.prevent="saveNewCategory">
            <div class="px-6 py-4">
                <div class="text-lg font-medium text-gray-900">
                    {{ __('New Category') }}
                </div>

                <div class="mt-4 space-y-2 text-sm text-gray-600">
                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="new-category-name" :value="__('Name')" />
                        <x-input wire:model="newCategory.name" id="new-category-name" type="text"
                            class="mt-1 block w-full" />
                        <x-input-error for="newCategory.name" class="mt-2" />
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="new-category-slug" :value="__('Slug')" />
                        <x-input wire:model="newCategory.slug" id="new-category-slug" type="text"
                            class="mt-1 block w-full" />
                        <x-input-error for="newCategory.slug" class="mt-2" />
                    </div>
                </div>
            </div>

            <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right space-x-2">
                <x-secondary-button wire:click="closeCategoryModal" class="!p-2.5">
                    Cancel
                </x-secondary-button>
                <x-button>{{ __('Submit') }}</x-button>
            </div>
        </form>
    </x-modal>
</div>
