@props(['wireModel', 'label' => 'Content', 'value' => ''])

<div wire:ignore>
    @if ($label)
        <label class="block text-xs font-semibold uppercase tracking-wide text-slate-400 mb-1.5">{{ $label }}</label>
    @endif
    <div class="rounded-lg border border-slate-300 overflow-hidden focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/50">
        <div class="flex flex-wrap items-center gap-1 bg-slate-50 border-b border-slate-200 p-1.5">
            <button type="button" @mousedown.prevent @click="document.execCommand('bold')" class="editor-btn" title="Bold"><i class="fa-solid fa-bold"></i></button>
            <button type="button" @mousedown.prevent @click="document.execCommand('italic')" class="editor-btn" title="Italic"><i class="fa-solid fa-italic"></i></button>
            <button type="button" @mousedown.prevent @click="document.execCommand('underline')" class="editor-btn" title="Underline"><i class="fa-solid fa-underline"></i></button>
            <span class="w-px h-5 bg-slate-200 mx-1"></span>
            <button type="button" @mousedown.prevent @click="document.execCommand('formatBlock', false, 'h2')" class="editor-btn text-xs font-bold" title="Heading">H2</button>
            <button type="button" @mousedown.prevent @click="document.execCommand('formatBlock', false, 'h3')" class="editor-btn text-xs font-bold" title="Subheading">H3</button>
            <button type="button" @mousedown.prevent @click="document.execCommand('formatBlock', false, 'p')" class="editor-btn text-xs" title="Paragraph">¶</button>
            <span class="w-px h-5 bg-slate-200 mx-1"></span>
            <button type="button" @mousedown.prevent @click="document.execCommand('insertUnorderedList')" class="editor-btn" title="Bullet list"><i class="fa-solid fa-list-ul"></i></button>
            <button type="button" @mousedown.prevent @click="document.execCommand('insertOrderedList')" class="editor-btn" title="Numbered list"><i class="fa-solid fa-list-ol"></i></button>
            <span class="w-px h-5 bg-slate-200 mx-1"></span>
            <button type="button" @mousedown.prevent @click="let u = prompt('Link URL'); if (u) document.execCommand('createLink', false, u)" class="editor-btn" title="Insert link"><i class="fa-solid fa-link"></i></button>
            <button type="button" @mousedown.prevent @click="document.execCommand('removeFormat')" class="editor-btn" title="Clear formatting"><i class="fa-solid fa-eraser"></i></button>
        </div>
        <div
            contenteditable="true"
            x-init="$el.innerHTML = @js($value)"
            @input="$wire.set('{{ $wireModel }}', $el.innerHTML)"
            @blur="$wire.set('{{ $wireModel }}', $el.innerHTML)"
            class="cms-content min-h-[180px] max-h-96 overflow-y-auto p-3 text-sm text-slate-800 focus:outline-none"
        ></div>
    </div>
</div>
