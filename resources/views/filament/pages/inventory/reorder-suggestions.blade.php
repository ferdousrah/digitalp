<x-filament-panels::page>
    @php $candidates = $this->getCandidates(); @endphp

    @if($candidates->isEmpty())
        <div class="ds-surface" style="padding:3rem; border-radius:1rem; text-align:center;">
            <div style="font-size:3rem; line-height:1; margin-bottom:0.5rem;">🎉</div>
            <h3 class="ds-text-strong" style="margin:0; font-size:1.1rem; font-weight:700;">No products need restocking</h3>
            <p class="ds-text-muted" style="margin-top:0.5rem;">Every active product has stock above its minimum threshold.</p>
        </div>
    @else
        <div style="display:flex; gap:0.5rem; margin-bottom:1rem; flex-wrap:wrap;">
            <button type="button" wire:click="selectAll"
                class="ds-chip"
                style="padding:0.45rem 1rem; border-radius:0.5rem; font-size:0.82rem; font-weight:600; cursor:pointer;">
                Select All
            </button>
            <button type="button" wire:click="clearAll"
                class="ds-chip"
                style="padding:0.45rem 1rem; border-radius:0.5rem; font-size:0.82rem; font-weight:600; cursor:pointer;">
                Clear
            </button>
            <div style="flex:1;"></div>
            <div class="ds-text-muted" style="align-self:center; font-size:0.85rem;">
                {{ count(array_filter($selected ?? [])) }} of {{ $candidates->count() }} selected
            </div>
        </div>

        <div class="ds-surface" style="border-radius:1rem; overflow:hidden;">
            <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr class="ds-text-muted" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em;">
                            <th style="padding:0.75rem 1rem; text-align:left; width:40px;"></th>
                            <th style="padding:0.75rem 1rem; text-align:left;">Product</th>
                            <th style="padding:0.75rem 1rem; text-align:left;">SKU</th>
                            <th style="padding:0.75rem 1rem; text-align:left;">Brand</th>
                            <th style="padding:0.75rem 1rem; text-align:right;">Stock</th>
                            <th style="padding:0.75rem 1rem; text-align:right;">Min</th>
                            <th style="padding:0.75rem 1rem; text-align:right;">Unit Cost</th>
                            <th style="padding:0.75rem 1rem; text-align:right; width:120px;">Order Qty</th>
                            <th style="padding:0.75rem 1rem; text-align:right;">Line Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($candidates as $c)
                            @php
                                $qty = $quantities[$c->id] ?? $c->suggested;
                                $checked = !empty($selected[$c->id]);
                            @endphp
                            <tr class="ds-divider" style="border-top:1px solid rgb(241 245 249);">
                                <td style="padding:0.6rem 1rem;">
                                    <input type="checkbox" wire:model.live="selected.{{ $c->id }}" style="width:16px; height:16px; cursor:pointer;">
                                </td>
                                <td class="ds-text-strong" style="padding:0.6rem 1rem; font-weight:600; font-size:0.88rem;">
                                    <a href="{{ url('/admin/products/' . $c->id . '/edit') }}" wire:navigate style="color:inherit; text-decoration:none;">{{ $c->name }}</a>
                                </td>
                                <td class="ds-text-muted" style="padding:0.6rem 1rem; font-family:monospace; font-size:0.82rem;">{{ $c->sku ?? '—' }}</td>
                                <td class="ds-text-muted" style="padding:0.6rem 1rem; font-size:0.85rem;">{{ $c->brand ?? '—' }}</td>
                                <td style="padding:0.6rem 1rem; text-align:right; font-weight:700; color:{{ $c->stock <= 0 ? '#ef4444' : '#f59e0b' }};">{{ $c->stock }}</td>
                                <td class="ds-text-muted" style="padding:0.6rem 1rem; text-align:right;">{{ $c->min }}</td>
                                <td class="ds-text-muted" style="padding:0.6rem 1rem; text-align:right;">৳{{ number_format($c->cost_price, 2) }}</td>
                                <td style="padding:0.6rem 1rem; text-align:right;">
                                    <input type="number" wire:model.live="quantities.{{ $c->id }}"
                                        min="1" value="{{ $qty }}"
                                        style="width:90px; padding:4px 8px; border:1px solid rgb(226 232 240); border-radius:6px; text-align:right; font-size:0.88rem;">
                                </td>
                                <td class="ds-text-strong" style="padding:0.6rem 1rem; text-align:right; font-weight:700;">
                                    ৳{{ number_format($c->cost_price * ($qty ?: $c->suggested), 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <p class="ds-text-muted" style="margin-top:1rem; font-size:0.82rem;">
            Suggested order quantity = <code>(2 × minimum stock) − current stock</code>. You can edit the quantity before creating a PO.
            Click <strong>Create PO from Selected</strong> in the top-right to build a draft purchase order for the selected items.
        </p>
    @endif
</x-filament-panels::page>
