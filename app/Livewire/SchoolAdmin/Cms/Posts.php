<?php

namespace App\Livewire\SchoolAdmin\Cms;

use App\Models\CmsPost;
use App\Support\HtmlSanitizer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class Posts extends Component
{
    use WithFileUploads;
    use WithPagination;

    public bool $showModal = false;

    public ?int $postId = null;

    public string $title = '';

    public string $slug = '';

    public string $excerpt = '';

    public string $content = '';

    public string $status = 'draft';

    public bool $publishNow = true;

    public string $publishDate = '';

    public $featuredImage = null;

    public ?string $existingFeaturedImageUrl = null;

    public function render(): View
    {
        return view('livewire.school-admin.cms.posts', [
            'posts' => CmsPost::where('school_id', auth()->user()->school_id)
                ->orderByDesc('id')
                ->paginate(10),
        ]);
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $post = CmsPost::where('school_id', auth()->user()->school_id)->findOrFail($id);

        $this->postId = $post->id;
        $this->title = $post->title;
        $this->slug = $post->slug;
        $this->excerpt = (string) $post->excerpt;
        $this->content = (string) $post->content;
        $this->status = $post->status;
        $this->publishNow = $post->published_at !== null && $post->published_at->lessThanOrEqualTo(now());
        $this->publishDate = $post->published_at?->format('Y-m-d\TH:i') ?? '';
        $this->existingFeaturedImageUrl = $post->featuredImageUrl();
        $this->featuredImage = null;
        $this->showModal = true;
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->postId) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $schoolId = auth()->user()->school_id;

        $validated = $this->validate([
            'title' => 'required|string|max:150',
            'slug' => ['required', 'alpha_dash', 'max:150', Rule::unique('cms_posts', 'slug')->where('school_id', $schoolId)->ignore($this->postId)],
            'excerpt' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'status' => 'required|in:draft,published',
            'publishDate' => 'nullable|date',
            'featuredImage' => 'nullable|image|max:2048',
        ]);

        $data = [
            'school_id' => $schoolId,
            'author_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'excerpt' => $validated['excerpt'] ?: null,
            'content' => HtmlSanitizer::clean($validated['content']),
            'status' => $validated['status'],
            'published_at' => $validated['status'] === 'published'
                ? ($this->publishNow ? now() : ($validated['publishDate'] ?: null))
                : null,
        ];

        if ($this->featuredImage) {
            $data['featured_image'] = $this->featuredImage->store('cms-posts', 'public');
        }

        CmsPost::updateOrCreate(['id' => $this->postId, 'school_id' => $schoolId], $data);

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        CmsPost::where('school_id', auth()->user()->school_id)->findOrFail($id)->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['postId', 'title', 'slug', 'excerpt', 'content', 'publishDate', 'featuredImage', 'existingFeaturedImageUrl']);
        $this->status = 'draft';
        $this->publishNow = true;
        $this->resetErrorBag();
    }
}
