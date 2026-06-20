@php
    /** WordPress-style media library grid picker with in-modal upload + delete.
     * Loads up to 200 latest items, filters client-side for instant search.
     */
    $items = \App\Models\MediaLibraryItem::with('media')
        ->orderByDesc('created_at')
        ->limit(200)
        ->get()
        ->map(function ($i) {
            $bytes = $i->fileSize();
            return [
                'id'          => $i->id,
                'title'       => $i->title ?: 'Untitled',
                'alt_text'    => $i->alt_text ?: '',
                'description' => $i->description ?: '',
                'thumb'       => $i->thumbUrl() ?: $i->url(),
                'tags'        => $i->tags ?: [],
                'file'        => $i->fileName(),
                'size'        => $bytes < 1024 * 1024
                    ? round($bytes / 1024, 1) . ' KB'
                    : round($bytes / 1024 / 1024, 2) . ' MB',
            ];
        })
        ->values();
@endphp

<div
    x-data="{
        items: {{ $items->toJson() }},
        search: '',
        hover: null,
        selectedId: null,
        uploading: false,
        uploadError: '',
        editing: null,
        saving: false,
        csrf: '{{ csrf_token() }}',
        uploadUrl: '{{ url('/admin/media-library/upload') }}',
        deleteBase: '{{ url('/admin/media-library') }}',
        get filtered() {
            if (!this.search) return this.items;
            const s = this.search.toLowerCase();
            return this.items.filter(i =>
                (i.title || '').toLowerCase().includes(s) ||
                (i.file || '').toLowerCase().includes(s) ||
                ((i.tags || []).join(' ').toLowerCase().includes(s))
            );
        },
        findStatePath() {
            const candidates = ['mountedActionsData.0.library_item_id','mountedFormComponentActionsData.0.library_item_id','mountedTableActionsData.0.library_item_id','data.library_item_id'];
            for (const p of candidates) { try { if (this.$wire.get(p) !== undefined) return p; } catch (e) {} }
            return 'mountedActionsData.0.library_item_id';
        },
        select(id) {
            this.selectedId = id;
            try {
                const path = this.findStatePath();
                if (path) this.$wire.set(path, String(id), false);
            } catch (e) {
                const inp = this.$root.closest('form')?.querySelector('input[name$=library_item_id]');
                if (inp) { inp.value = id; inp.dispatchEvent(new Event('input', { bubbles: true })); inp.dispatchEvent(new Event('change', { bubbles: true })); }
            }
        },
        syncFromWire() {
            const path = this.findStatePath();
            if (path) { const v = this.$wire.get(path); if (v) this.selectedId = parseInt(v); }
        },
        async uploadFiles(files) {
            if (!files || !files.length) return;
            this.uploading = true; this.uploadError = '';
            for (const file of files) {
                if (!file.type.startsWith('image/')) continue;
                const fd = new FormData(); fd.append('file', file);
                try {
                    const res = await fetch(this.uploadUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' }, body: fd });
                    if (res.ok) {
                        const item = await res.json();
                        if (!this.items.some(i => i.id === item.id)) this.items.unshift(item);
                        this.select(item.id);
                    } else {
                        const j = await res.json().catch(() => ({}));
                        this.uploadError = j.message || ('Upload failed (' + res.status + ')');
                    }
                } catch (e) { this.uploadError = 'Upload failed.'; }
            }
            this.uploading = false;
        },
        async removeItem(id, ev) {
            ev.stopPropagation();
            if (!confirm('Delete this file from the library permanently? This cannot be undone.')) return;
            try {
                const res = await fetch(this.deleteBase + '/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json' } });
                if (res.ok) {
                    this.items = this.items.filter(i => i.id !== id);
                    if (this.selectedId === id) this.selectedId = null;
                }
            } catch (e) {}
        },
        startEdit(item, ev) {
            ev.stopPropagation();
            this.editing = {
                id: item.id,
                title: item.title || '',
                alt_text: item.alt_text || '',
                tags: (item.tags || []).join(', '),
                description: item.description || '',
            };
        },
        cancelEdit() { this.editing = null; this.saving = false; },
        async saveEdit() {
            if (!this.editing) return;
            this.saving = true;
            try {
                const res = await fetch(this.deleteBase + '/' + this.editing.id, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': this.csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ title: this.editing.title, alt_text: this.editing.alt_text, tags: this.editing.tags, description: this.editing.description }),
                });
                if (res.ok) {
                    const updated = await res.json();
                    const idx = this.items.findIndex(i => i.id === updated.id);
                    if (idx !== -1) this.items[idx] = updated;
                    this.editing = null;
                }
            } catch (e) {}
            this.saving = false;
        }
    }"
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
            <span x-text="filtered.length"></span> of <span x-text="items.length"></span>
        </div>
        <a href="{{ url('/admin/media-library-items') }}" target="_blank"
           style="font-size:0.82rem; color:#16a34a; font-weight:600; text-decoration:none;">
            ↗ Open full Media Library
        </a>
    </div>

    {{-- Drag & drop upload zone --}}
    <div
        @dragover.prevent="$el.style.borderColor='#16a34a'; $el.style.background='#f0fdf4'"
        @dragleave.prevent="$el.style.borderColor='#cbd5e1'; $el.style.background='#fff'"
        @drop.prevent="$el.style.borderColor='#cbd5e1'; $el.style.background='#fff'; uploadFiles($event.dataTransfer.files)"
        style="border:2px dashed #cbd5e1; border-radius:0.5rem; padding:12px; text-align:center; font-size:0.85rem; color:#64748b; background:#fff; transition:border-color 0.15s, background 0.15s;"
    >
        <span x-show="!uploading">
            <svg style="width:18px; height:18px; display:inline-block; vertical-align:-3px; color:#16a34a;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
            Drag &amp; drop images here, or
            <button type="button" @click="$refs.fileInput.click()" style="color:#16a34a; font-weight:700; background:none; border:none; cursor:pointer; padding:0; font-size:0.85rem;">browse</button>
            to add to the library
        </span>
        <span x-show="uploading" style="color:#16a34a; font-weight:600;">Uploading…</span>
        <input x-ref="fileInput" type="file" accept="image/*" multiple style="display:none"
               @change="uploadFiles($event.target.files); $event.target.value=''">
    </div>
    <p x-show="uploadError" x-text="uploadError" x-cloak style="color:#ef4444; font-size:0.78rem; margin:-4px 0 0;"></p>

    {{-- Grid --}}
    <div style="flex:1; overflow-y:auto; padding:0.25rem; background:#f8fafc; border:1px solid rgb(226 232 240); border-radius:0.5rem; position:relative;">

        {{-- Edit details overlay --}}
        <div x-show="editing" x-cloak @click.stop
             style="position:absolute; inset:0; z-index:6; background:#fff; padding:1.25rem; overflow-y:auto;">
            <template x-if="editing">
                <div style="max-width:560px; margin:0 auto; display:flex; flex-direction:column; gap:0.85rem;">
                    <h3 style="font-size:1rem; font-weight:700; color:#0f172a; margin:0;">Edit image details</h3>
                    <div>
                        <label style="display:block; font-size:0.78rem; font-weight:600; color:#475569; margin-bottom:4px;">Title</label>
                        <input x-model="editing.title" type="text" style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:0.5rem; font-size:0.9rem; box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.78rem; font-weight:600; color:#475569; margin-bottom:4px;">Alt text <span style="color:#94a3b8; font-weight:400;">(image SEO / accessibility)</span></label>
                        <input x-model="editing.alt_text" type="text" style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:0.5rem; font-size:0.9rem; box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.78rem; font-weight:600; color:#475569; margin-bottom:4px;">Tags <span style="color:#94a3b8; font-weight:400;">(comma-separated)</span></label>
                        <input x-model="editing.tags" type="text" placeholder="skincare, banner, summer" style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:0.5rem; font-size:0.9rem; box-sizing:border-box;">
                    </div>
                    <div>
                        <label style="display:block; font-size:0.78rem; font-weight:600; color:#475569; margin-bottom:4px;">Description</label>
                        <textarea x-model="editing.description" rows="3" style="width:100%; padding:8px 12px; border:1px solid #cbd5e1; border-radius:0.5rem; font-size:0.9rem; resize:vertical; box-sizing:border-box;"></textarea>
                    </div>
                    <div style="display:flex; gap:0.5rem; margin-top:0.25rem;">
                        <button type="button" @click="saveEdit()" :disabled="saving"
                                :style="saving ? 'opacity:0.6; cursor:wait;' : ''"
                                style="padding:9px 22px; background:#16a34a; color:#fff; border:none; border-radius:0.5rem; font-weight:700; font-size:0.85rem; cursor:pointer;">
                            <span x-text="saving ? 'Saving…' : 'Save details'"></span>
                        </button>
                        <button type="button" @click="cancelEdit()" style="padding:9px 22px; background:#f1f5f9; color:#334155; border:none; border-radius:0.5rem; font-weight:600; font-size:0.85rem; cursor:pointer;">Cancel</button>
                    </div>
                </div>
            </template>
        </div>

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

                        {{-- Edit + Delete (on hover) --}}
                        <div x-show="hover === item.id" style="position:absolute; top:6px; left:6px; display:flex; gap:4px; z-index:2;">
                            <button type="button" @click="startEdit(item, $event)" title="Edit details"
                                    style="width:24px; height:24px; background:rgba(15,23,42,0.85); border:2px solid #fff; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer;">
                                <svg style="width:12px; height:12px; color:#fff;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/></svg>
                            </button>
                            <button type="button" @click="removeItem(item.id, $event)" title="Delete from library"
                                    style="width:24px; height:24px; background:rgba(220,38,38,0.92); border:2px solid #fff; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer;">
                                <svg style="width:12px; height:12px; color:#fff;" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

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

        {{-- Empty / no-match states (client-side) --}}
        <div x-show="items.length === 0" style="padding:3rem; text-align:center; color:#64748b;">
            <div style="font-size:2.5rem;">🖼️</div>
            <p style="margin-top:0.5rem; font-weight:600;">Your media library is empty</p>
            <p style="font-size:0.85rem;">Drag &amp; drop images above to upload.</p>
        </div>
        <div x-show="items.length > 0 && filtered.length === 0 && search" style="padding:3rem; text-align:center; color:#64748b;">
            <div style="font-size:2rem;">🔍</div>
            <p style="margin-top:0.5rem;">No files match "<span x-text="search" style="font-weight:600;"></span>"</p>
        </div>
    </div>

    {{-- Footer hint --}}
    <div style="font-size:0.78rem; color:#64748b; padding:0 0.25rem;">
        Click a file to select it, then click <strong>Use selected file</strong> below. The file is copied into this record so the library stays independent.
    </div>
</div>
