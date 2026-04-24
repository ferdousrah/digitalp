@props(['section'])
@php
    $content  = $section->extra['content'] ?? '';
    $maxH     = $section->extra['seo_collapsed_height'] ?? 160;
    $uid      = 'seo' . $section->id;
    $primaryColor = \App\Services\SettingService::get('color_primary', '#16a34a');
@endphp
@once
<style>
.seo-content h1, .seo-content h2, .seo-content h3,
.seo-content h4, .seo-content h5, .seo-content h6 {
    font-weight: 700;
    color: #111827;
    margin-top: 1.4em;
    margin-bottom: 0.4em;
    line-height: 1.3;
}
.seo-content h1 { font-size: 1.6rem; }
.seo-content h2 { font-size: 1.3rem; }
.seo-content h3 { font-size: 1.1rem; }
.seo-content h4 { font-size: 1rem; }
.seo-content p  { margin-bottom: 0.8em; }
.seo-content ul, .seo-content ol { padding-left: 1.4em; margin-bottom: 0.8em; }
.seo-content ul { list-style: disc; }
.seo-content ol { list-style: decimal; }
.seo-content li { margin-bottom: 0.3em; }
.seo-content a  { color: {{ $primaryColor }}; text-decoration: underline; }
.seo-content strong { font-weight: 700; color: #374151; }
.seo-content blockquote { border-left: 3px solid {{ $primaryColor }}; padding-left: 1em; color: #6b7280; margin: 1em 0; font-style: italic; }
.seo-content code { background: #f3f4f6; padding: 0.1em 0.4em; border-radius: 4px; font-size: 0.85em; }
.seo-content pre  { background: #f3f4f6; padding: 1em; border-radius: 6px; overflow-x: auto; margin-bottom: 0.8em; }
.seo-content mark { background: var(--tiptap-highlight, #fef08a); padding: 0.1em 0.2em; border-radius: 2px; }
.seo-content .lead { font-size: 1.15em; color: #374151; }
.seo-content small, .seo-content .small { font-size: 0.8em; }
</style>
@endonce
@if($content)
<section style="background:{{ $section->bg_color }}; padding:{{ $section->padding_y }}px 0;">
    <div class="container-custom px-4 sm:px-6 lg:px-8">

        <div id="{{ $uid }}-wrap" style="position:relative;">
            {{-- Content --}}
            <div id="{{ $uid }}-body"
                style="max-height:{{ $maxH }}px; overflow:hidden; transition:max-height 0.5s cubic-bezier(0.4,0,0.2,1);">
                <div class="seo-content" style="color:#6b7280; line-height:1.8; font-size:0.9rem; text-align:{{ $section->extra['content_align'] ?? $section->text_align ?? 'left' }};">
                    {!! $content !!}
                </div>
            </div>

            {{-- Fade overlay --}}
            <div id="{{ $uid }}-fade"
                style="position:absolute; bottom:0; left:0; right:0; height:60px; background:linear-gradient(to bottom, transparent, {{ $section->bg_color }}); pointer-events:none; transition:opacity 0.3s;">
            </div>
        </div>

        {{-- Toggle button --}}
        <div style="text-align:{{ $section->extra['content_align'] ?? $section->text_align ?? 'center' }}; margin-top:12px;">
            <button id="{{ $uid }}-btn" onclick="{{ $uid }}Toggle()"
                style="display:inline-flex; align-items:center; gap:6px; background:none; border:1.5px solid {{ $primaryColor }}; color:{{ $primaryColor }}; font-size:0.8rem; font-weight:700; letter-spacing:0.07em; text-transform:uppercase; padding:6px 18px; border-radius:20px; cursor:pointer; transition:background 0.2s, color 0.2s;"
                onmouseover="this.style.background='{{ $primaryColor }}';this.style.color='#fff'"
                onmouseout="this.style.background='none';this.style.color='{{ $primaryColor }}'">
                <span id="{{ $uid }}-label">Read More</span>
                <svg id="{{ $uid }}-arrow" style="width:14px;height:14px;transition:transform 0.3s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

    </div>
</section>

<script>
(function() {
    var body    = document.getElementById('{{ $uid }}-body');
    var fade    = document.getElementById('{{ $uid }}-fade');
    var label   = document.getElementById('{{ $uid }}-label');
    var arrow   = document.getElementById('{{ $uid }}-arrow');
    var open    = false;
    var natural = body.scrollHeight;

    // Hide toggle if content fits
    if (natural <= {{ $maxH }}) {
        document.getElementById('{{ $uid }}-btn').style.display = 'none';
        fade.style.display = 'none';
    }

    window.{{ $uid }}Toggle = function() {
        open = !open;
        body.style.maxHeight = open ? natural + 'px' : '{{ $maxH }}px';
        fade.style.opacity   = open ? '0' : '1';
        label.textContent    = open ? 'Show Less' : 'Read More';
        arrow.style.transform = open ? 'rotate(180deg)' : 'rotate(0deg)';
    };
})();
</script>
@endif
