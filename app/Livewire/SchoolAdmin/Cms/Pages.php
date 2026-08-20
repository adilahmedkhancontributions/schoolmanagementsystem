<?php

namespace App\Livewire\SchoolAdmin\Cms;

use App\Models\CmsPage;
use App\Support\HtmlSanitizer;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
class Pages extends Component
{
    use WithPagination;

    public bool $showModal = false;

    public ?int $pageId = null;

    public string $title = '';

    public string $slug = '';

    public string $content = '';

    public string $metaTitle = '';

    public string $metaDescription = '';

    public string $status = 'draft';

    public function render(): View
    {
        return view('livewire.school-admin.cms.pages', [
            'pages' => CmsPage::where('school_id', auth()->user()->school_id)
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
        $page = CmsPage::where('school_id', auth()->user()->school_id)->findOrFail($id);

        $this->pageId = $page->id;
        $this->title = $page->title;
        $this->slug = $page->slug;
        $this->content = (string) $page->content;
        $this->metaTitle = (string) $page->meta_title;
        $this->metaDescription = (string) $page->meta_description;
        $this->status = $page->status;
        $this->showModal = true;
    }

    public function updatedTitle(string $value): void
    {
        if (! $this->pageId) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $schoolId = auth()->user()->school_id;

        $validated = $this->validate([
            'title' => 'required|string|max:150',
            'slug' => ['required', 'alpha_dash', 'max:150', Rule::unique('cms_pages', 'slug')->where('school_id', $schoolId)->ignore($this->pageId)],
            'content' => 'nullable|string',
            'metaTitle' => 'nullable|string|max:160',
            'metaDescription' => 'nullable|string|max:255',
            'status' => 'required|in:draft,published',
        ]);

        $existing = $this->pageId ? CmsPage::find($this->pageId) : null;
        $publishedAt = $validated['status'] === 'published' ? ($existing?->published_at ?? now()) : null;

        CmsPage::updateOrCreate(
            ['id' => $this->pageId, 'school_id' => $schoolId],
            [
                'school_id' => $schoolId,
                'title' => $validated['title'],
                'slug' => $validated['slug'],
                'content' => HtmlSanitizer::clean($validated['content']),
                'meta_title' => $validated['metaTitle'] ?: null,
                'meta_description' => $validated['metaDescription'] ?: null,
                'status' => $validated['status'],
                'published_at' => $publishedAt,
            ]
        );

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        CmsPage::where('school_id', auth()->user()->school_id)->findOrFail($id)->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['pageId', 'title', 'slug', 'content', 'metaTitle', 'metaDescription']);
        $this->status = 'draft';
        $this->resetErrorBag();
    }
}
