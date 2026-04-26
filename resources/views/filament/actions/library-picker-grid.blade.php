@php
    /** WordPress-style media library grid picker.
     * Loads up to 200 latest items, filters client-side for instant search.
     */
    $items = \App\Models\MediaLibraryItem::with('media')
        ->orderByDesc('created_at')
        ->limit(200)
        ->get()
        ->map(function ($i) {
            $thumb = $i->thumbUrl();
            $bytes = $i->fileSize();
            $size  = $bytes < 1024 * 1024
                ? round($bytes / 1024, 1) . ' KB'
                : round($bytes / 1024 / 1024, 2) . ' MB';
            return [
                'id'    => $i->id,
                'title' => $i->title ?: 'Untitled',
                'thumb' => $thumb,
                'tags'  => $i->tags ?: [],
                'file'  => $i->fileName(),
                'size'  => $size,
            ];
        })
        ->values();
@endphp

<div
    x-data="libraryPicker({{ $items->toJson() }})"
    x-init="syncFromWire()"
    style="display:flex; flex-direction:column; gap:0.75rem; min-height:60vh; max-height:70vh;"
>
    {{-- Toolbar --}}
    <div style="display:flex; gap:0.75rem; align-items:center; flex-wrap:wrap;">
        <input
            type="search"
            x-model.debounce.150ms="search"
            placeholder="Search by title, tag, or filename…"
            style="flex:1; min-width:200px; padding:8px 12px; border:1px solid rgb(226 232 240); border-radius:0.5rem; font-size:0.9rem; background:#fff; color:#111;"
        >
        <div style="font-size:0.78rem; color:#64748b;">
            <span x-text="filtered.length"></span> of {{ count($items) }}
        </div>
        <a href="{{ url('/admin/media-library-items') }}" target="_blank"
           style="font-size:0.82rem; color:#16a34a; font-weight:600; text-decoration:none;">
            ↗ Open full Media Library
        </a>
    </div>

    {{-- Empty state --}}
    @if(count($items) === 0)
        <div style="flex:1; display:flex; align-items:center; justify-content:center; padding:3rem; text-align:center; background:#f9fafb; border-radius:0.75rem;">
            <div>
                <div style="font-size:3rem;">📭</div>
                <h3 style="margin:0.5rem 0 0.25rem; font-weight:700;">Your media library is empty</h3>
                <p style="margin:0 0 1rem; color:#64748b; font-size:0.88rem;">Upload files first, then come back to pick from them.</p>
                <a href="{{ url('/admin/media-library-items') }}" target="_blank"
                   style="display:inline-block; padding:8px 16px; background:#16a34a; color:#fff; border-radius:6px; font-weight:600; text-decoration:none;">
                    Open Media Library →
                </a>
            </div>
        </div>
    @else
        {{-- Grid --}}
        <div style="flex:1; overflow-y:auto; padding:0.25rem; background:#f8fafc; border:1px solid rgb(226 232 240); border-radius:0.5rem;">
            <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(140px, 1fr)); gap:0.625rem; padding:0.5rem;">
                <template x-for="item in filtered" :key="item.id">
                    <div
                        @click="select(item.id)"
                        :style="selectedId === item.id
                            ? 'cursor:pointer; border:3px solid #16a34a; border-radius:0.5rem; overflow:hidden; background:#fff; transform:scale(0.98); box-shadow:0 0 0 4px rgba(22,163,74,0.18);'
                            : 'cursor:pointer; border:3px solid transparent; border-radius:0.5rem; overflow:hidden; background:#fff; transition:all 0.15s; box-shadow:0 1px 2px rgba(0,0,0,0.04);'
                        "
                        @mouseenter="hover = item.id" @mouseleave="hover = null"
                    >
                        <div style="position:relative; aspect-ratio:1/1; background:#f1f5f9;">
                            <template x-if="item.thumb">
                                <img :src="item.thumb" :alt="item.title"
                                     style="width:100%; height:100%; object-fit:cover; display:block;"
                                     loading="lazy" decoding="async">
                            </template>
                            <template x-if="!item.thumb">
                                <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:#94a3b8;">
                                    <svg style="width:32px; height:32px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13.5h6m-3-3v6m-9 1.5V6.75A2.25 2.25 0 015.25 4.5h13.5a2.25 2.25 0 012.25 2.25v10.5a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 17.25z"/></svg>
                                </div>
                            </template>

                            {{-- Selected check --}}
                            <div x-show="selectedId === item.id"
                                 style="position:absolute; top:6px; right:6px; width:24px; height:24px; background:#16a34a; border:2px solid #fff; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                <svg style="width:14px; height:14px; color:#fff;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            </div>
                        </div>
                        <div style="padding:6px 8px;">
                            <div x-text="item.title"
                                 style="font-size:0.78rem; font-weight:600; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; line-height:1.3;"></div>
                            <div x-text="item.size"
                                 style="font-size:0.7rem; color:#64748b; line-height:1.2; margin-top:1px;"></div>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="filtered.length === 0 && search" style="padding:3rem; text-align:center; color:#64748b;">
                <div style="font-size:2rem;">🔍</div>
                <p style="margin-top:0.5rem;">No files match "<span x-text="search" style="font-weight:600;"></span>"</p>
            </div>
        </div>

        {{-- Footer hint --}}
        <div style="font-size:0.78rem; color:#64748b; padding:0 0.25rem;">
            Click a file to select it, then click <strong>Use selected file</strong> below. The file will be copied into this record so the library stays independent.
        </div>
    @endif
</div>

<script>
    if (typeof window.libraryPicker === 'undefined') {
        window.libraryPicker = function (items) {
            return {
                items: items,
                search: '',
                hover: null,
                selectedId: null,
                get filtered() {
                    if (!this.search) return this.items;
                    const s = this.search.toLowerCase();
                    return this.items.filter(i =>
                        (i.title || '').toLowerCase().includes(s) ||
                        (i.file || '').toLowerCase().includes(s) ||
                        ((i.tags || []).join(' ').toLowerCase().includes(s))
                    );
                },
                select(id) {
                    this.selectedId = id;
                    // Filament's action form is mounted as mountedActions[N].data
                    // We update the hidden library_item_id field via $wire.set so the
                    // action handler receives it on submit.
                    try {
                        const path = this.findStatePath();
                        if (path) {
                            this.$wire.set(path, String(id), false);
                        }
                    } catch (e) {
                        // Fallback: dispatch on hidden input directly
                        const inp = this.$root.closest('form')?.querySelector('input[type="hidden"][wire\\:model$="library_item_id"], input[type="hidden"][wire\\:model\\.live$="library_item_id"], input[type="hidden"][name$="library_item_id"]');
                        if (inp) {
                            inp.value = id;
                            inp.dispatchEvent(new Event('input', { bubbles: true }));
                            inp.dispatchEvent(new Event('change', { bubbles: true }));
                        }
                    }
                },
                syncFromWire() {
                    // If form already had a value (re-opened modal), reflect it visually
                    const path = this.findStatePath();
                    if (path) {
                        const v = this.$wire.get(path);
                        if (v) this.selectedId = parseInt(v);
                    }
                },
                /** Walk likely Filament action data paths to find the right one. */
                findStatePath() {
                    const candidates = [
                        'mountedActionsData.0.library_item_id',
                        'mountedFormComponentActionsData.0.library_item_id',
                        'mountedTableActionsData.0.library_item_id',
                        'data.library_item_id',
                    ];
                    for (const p of candidates) {
                        try {
                            const val = this.$wire.get(p);
                            if (val !== undefined) return p;
                        } catch (e) { /* continue */ }
                    }
                    // Default to the most common path
                    return 'mountedActionsData.0.library_item_id';
                },
            };
        };
    }
</script>
