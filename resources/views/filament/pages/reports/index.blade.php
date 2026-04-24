<x-filament-panels::page>
    <h3 class="ds-text-strong" style="margin:0 0 0.75rem; font-size:0.8rem; font-weight:700; letter-spacing:0.06em; text-transform:uppercase;">Business Reports</h3>
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:1rem; margin-bottom:2rem;">
        @foreach($this->getReports() as $r)
            <a href="{{ $r['href'] }}" wire:navigate class="ds-surface" style="
                display:flex; flex-direction:column; gap:0.5rem;
                padding:1.5rem;
                border-radius:1rem;
                text-decoration:none;
                box-shadow:0 1px 3px rgba(0,0,0,0.04);
                transition:transform 0.25s cubic-bezier(.4,0,.2,1), box-shadow 0.25s ease, border-color 0.2s;
                position:relative;
                overflow:hidden;
            "
            onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px -8px rgba(0,0,0,0.1), 0 4px 8px -2px rgba(0,0,0,0.04)';"
            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.04)';">

                <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.25rem;">
                    <div style="width:40px; height:40px; flex:0 0 40px; border-radius:0.625rem; background:{{ $r['color'] }}22; color:{{ $r['color'] }}; display:flex; align-items:center; justify-content:center;">
                        <x-dynamic-component :component="$r['icon']" style="width:22px; height:22px;" />
                    </div>
                    <h3 class="ds-text-strong" style="margin:0; font-size:1.05rem; font-weight:700; letter-spacing:-0.01em;">{{ $r['label'] }}</h3>
                </div>

                <p class="ds-text-muted" style="margin:0; font-size:0.875rem; line-height:1.5;">{{ $r['description'] }}</p>

                <div class="ds-text-muted" style="margin-top:auto; padding-top:0.75rem; font-size:0.78rem; font-weight:600; display:flex; align-items:center; gap:0.25rem; color:{{ $r['color'] }};">
                    <span>Open report</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px; height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </div>
            </a>
        @endforeach
    </div>

    @if(count($this->getAccountsReports()))
        <h3 class="ds-text-strong" style="margin:0 0 0.75rem; font-size:0.8rem; font-weight:700; letter-spacing:0.06em; text-transform:uppercase;">Accounts Reports</h3>
        <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:1rem;">
            @foreach($this->getAccountsReports() as $r)
                <a href="{{ $r['href'] }}" wire:navigate class="ds-surface" style="
                    display:flex; flex-direction:column; gap:0.5rem;
                    padding:1.5rem;
                    border-radius:1rem;
                    text-decoration:none;
                    box-shadow:0 1px 3px rgba(0,0,0,0.04);
                    transition:transform 0.25s cubic-bezier(.4,0,.2,1), box-shadow 0.25s ease, border-color 0.2s;
                    position:relative;
                    overflow:hidden;
                "
                onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 12px 24px -8px rgba(0,0,0,0.1), 0 4px 8px -2px rgba(0,0,0,0.04)';"
                onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.04)';">

                    <div style="display:flex; align-items:center; gap:0.75rem; margin-bottom:0.25rem;">
                        <div style="width:40px; height:40px; flex:0 0 40px; border-radius:0.625rem; background:{{ $r['color'] }}22; color:{{ $r['color'] }}; display:flex; align-items:center; justify-content:center;">
                            <x-dynamic-component :component="$r['icon']" style="width:22px; height:22px;" />
                        </div>
                        <h3 class="ds-text-strong" style="margin:0; font-size:1.05rem; font-weight:700; letter-spacing:-0.01em;">{{ $r['label'] }}</h3>
                    </div>

                    <p class="ds-text-muted" style="margin:0; font-size:0.875rem; line-height:1.5;">{{ $r['description'] }}</p>

                    <div class="ds-text-muted" style="margin-top:auto; padding-top:0.75rem; font-size:0.78rem; font-weight:600; display:flex; align-items:center; gap:0.25rem; color:{{ $r['color'] }};">
                        <span>Open report</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:12px; height:12px;"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
