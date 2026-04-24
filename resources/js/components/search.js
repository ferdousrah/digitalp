export function initSearch() {
    const searchInput   = document.querySelector('[data-search-input]');
    const searchResults = document.querySelector('[data-search-results]');
    if (!searchInput || !searchResults) return;

    const autocompleteUrl = searchInput.dataset.autocompleteUrl || '/search/autocomplete';
    const baseUrl = searchInput.closest('form')?.action || '/search';
    let debounceTimer;

    function show(html) {
        searchResults.innerHTML = html;
        searchResults.classList.remove('hidden');
    }

    function hide() {
        searchResults.classList.add('hidden');
        searchResults.innerHTML = '';
    }

    searchInput.addEventListener('input', (e) => {
        clearTimeout(debounceTimer);
        const query = e.target.value.trim();

        if (query.length < 2) { hide(); return; }

        debounceTimer = setTimeout(async () => {
            try {
                const res  = await fetch(`${autocompleteUrl}?q=${encodeURIComponent(query)}`);
                const data = await res.json();

                if (data.length === 0) {
                    show(`<div style="padding:14px 16px; color:#9ca3af; font-size:0.875rem; text-align:center;">No products found for "<strong style="color:#374151;">${escHtml(query)}</strong>"</div>`);
                    return;
                }

                const items = data.map(item => {
                    const img = item.image
                        ? `<img src="${escHtml(item.image)}" alt="${escHtml(item.name)}" style="width:48px; height:48px; object-fit:cover; border-radius:6px; flex-shrink:0; border:1px solid #f0f0f0;">`
                        : `<span style="width:48px; height:48px; border-radius:6px; background:#f3f4f6; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                               <svg style="width:22px;height:22px;color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                           </span>`;

                    const priceHtml = item.compare_price
                        ? `<span style="font-size:0.875rem; font-weight:700; color:#f97316;">৳${item.price}</span>
                           <span style="font-size:0.8rem; color:#9ca3af; text-decoration:line-through; margin-left:6px;">৳${item.compare_price}</span>`
                        : `<span style="font-size:0.875rem; font-weight:700; color:#f97316;">৳${item.price}</span>`;

                    const brandHtml = item.brand
                        ? `<div style="font-size:0.75rem; color:#9ca3af; margin-top:2px;">${escHtml(item.brand)}</div>`
                        : '';

                    return `<a href="${escHtml(item.url)}"
                                style="display:flex; align-items:center; gap:12px; padding:10px 16px; text-decoration:none; color:#111827; transition:background 0.15s; border-bottom:1px solid #f3f4f6;"
                                onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background='transparent'">
                                ${img}
                                <div style="flex:1; min-width:0;">
                                    <div style="font-size:0.875rem; font-weight:600; color:#111827; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; line-height:1.3;">${escHtml(item.name)}</div>
                                    <div style="display:flex; align-items:baseline; margin-top:3px;">${priceHtml}</div>
                                    ${brandHtml}
                                </div>
                            </a>`;
                }).join('');

                const footer = `<a href="${baseUrl}?q=${encodeURIComponent(query)}"
                                    style="display:flex; align-items:center; justify-content:center; gap:6px; padding:11px 14px; font-size:0.8rem; font-weight:600; color:#16a34a; text-decoration:none; background:#f9fafb; border-radius:0 0 16px 16px; transition:background 0.15s;"
                                    onmouseover="this.style.background='#f0fdf4'" onmouseout="this.style.background='#f9fafb'">
                                    See all results for "<em>${escHtml(query)}</em>"
                                    <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                </a>`;

                show(items + footer);
            } catch (err) {
                console.error('Search error:', err);
            }
        }, 280);
    });

    // Close on outside click
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            hide();
        }
    });

    // Close on Escape
    searchInput.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') { hide(); searchInput.blur(); }
    });
}

function escHtml(str) {
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
