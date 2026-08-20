<?php

namespace App\Livewire\SchoolAdmin\Cms;

use App\Models\GalleryImage;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.dashboard')]
class Gallery extends Component
{
    use WithFileUploads;

    public $newImages = [];

    public string $caption = '';

    public function render(): View
    {
        return view('livewire.school-admin.cms.gallery', [
            'images' => GalleryImage::where('school_id', auth()->user()->school_id)
                ->orderBy('sort_order')
                ->orderByDesc('id')
                ->get(),
        ]);
    }

    public function upload(): void
    {
        $this->validate([
            'newImages' => 'required|array|min:1',
            'newImages.*' => 'image|max:2048',
        ]);

        $schoolId = auth()->user()->school_id;
        $nextOrder = (int) GalleryImage::where('school_id', $schoolId)->max('sort_order') + 1;

        foreach ($this->newImages as $image) {
            GalleryImage::create([
                'school_id' => $schoolId,
                'image' => $image->store('cms-gallery', 'public'),
                'caption' => $this->caption ?: null,
                'sort_order' => $nextOrder++,
            ]);
        }

        $this->reset(['newImages', 'caption']);
    }

    public function moveUp(int $id): void
    {
        $this->swapOrder($id, 'up');
    }

    public function moveDown(int $id): void
    {
        $this->swapOrder($id, 'down');
    }

    private function swapOrder(int $id, string $direction): void
    {
        $schoolId = auth()->user()->school_id;
        $image = GalleryImage::where('school_id', $schoolId)->findOrFail($id);

        $neighbor = GalleryImage::where('school_id', $schoolId)
            ->when($direction === 'up', fn ($q) => $q->where('sort_order', '<', $image->sort_order)->orderByDesc('sort_order'))
            ->when($direction === 'down', fn ($q) => $q->where('sort_order', '>', $image->sort_order)->orderBy('sort_order'))
            ->first();

        if (! $neighbor) {
            return;
        }

        [$imageOrder, $neighborOrder] = [$image->sort_order, $neighbor->sort_order];
        $image->update(['sort_order' => $neighborOrder]);
        $neighbor->update(['sort_order' => $imageOrder]);
    }

    public function delete(int $id): void
    {
        GalleryImage::where('school_id', auth()->user()->school_id)->findOrFail($id)->delete();
    }
}
