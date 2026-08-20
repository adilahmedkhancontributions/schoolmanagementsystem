<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\CmsPage;
use App\Models\CmsPost;
use App\Models\ContactMessage;
use App\Models\GalleryImage;
use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicSiteController extends Controller
{
    public function home(School $school): View
    {
        return view('cms.public.home', [
            'school' => $school,
            'pages' => CmsPage::where('school_id', $school->id)->published()->orderBy('title')->get(),
            'posts' => CmsPost::where('school_id', $school->id)->published()->latest('published_at')->take(3)->get(),
            'images' => GalleryImage::where('school_id', $school->id)->orderBy('sort_order')->take(8)->get(),
            'announcements' => Announcement::where('school_id', $school->id)
                ->where('audience', 'everyone')
                ->whereNotNull('published_at')
                ->where('published_at', '<=', now())
                ->latest('published_at')
                ->take(3)
                ->get(),
        ]);
    }

    public function page(School $school, string $slug): View
    {
        $page = CmsPage::where('school_id', $school->id)
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        return view('cms.public.page', [
            'school' => $school,
            'pages' => CmsPage::where('school_id', $school->id)->published()->orderBy('title')->get(),
            'page' => $page,
        ]);
    }

    public function blogIndex(School $school): View
    {
        return view('cms.public.blog-index', [
            'school' => $school,
            'pages' => CmsPage::where('school_id', $school->id)->published()->orderBy('title')->get(),
            'posts' => CmsPost::where('school_id', $school->id)->published()->latest('published_at')->paginate(9),
        ]);
    }

    public function blogShow(School $school, string $slug): View
    {
        $post = CmsPost::where('school_id', $school->id)
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        return view('cms.public.blog-show', [
            'school' => $school,
            'pages' => CmsPage::where('school_id', $school->id)->published()->orderBy('title')->get(),
            'post' => $post,
        ]);
    }

    public function gallery(School $school): View
    {
        return view('cms.public.gallery', [
            'school' => $school,
            'pages' => CmsPage::where('school_id', $school->id)->published()->orderBy('title')->get(),
            'images' => GalleryImage::where('school_id', $school->id)->orderBy('sort_order')->get(),
        ]);
    }

    public function contact(Request $request, School $school): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:30',
            'message' => 'required|string|max:2000',
            'website' => 'prohibited',
        ]);

        ContactMessage::create([
            'school_id' => $school->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'message' => $validated['message'],
        ]);

        return back()->with('contactSent', true);
    }
}
