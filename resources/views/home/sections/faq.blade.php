@if($faqs->isNotEmpty())
<section style="background:{{ $section->bg_color ?? '#f8fafc' }}; padding:{{ $section->padding_y ?? 64 }}px 0;">
<div class="container-custom">

    @include('home.sections._section-header', ['section' => $section])

    @php $contentAlign = $section->extra['content_align'] ?? 'left'; @endphp
    {{-- FAQ Accordion --}}
    <div style="max-width:780px; margin:0 auto; display:flex; flex-direction:column; gap:12px; text-align:{{ $contentAlign }};">
        @foreach($faqs as $faq)
        <div class="faq-item" style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,0.04); transition:box-shadow 0.2s;">
            <button
                onclick="toggleFaq(this)"
                aria-expanded="false"
                style="width:100%; text-align:left; padding:20px 24px; background:none; border:none; cursor:pointer; display:flex; align-items:center; justify-content:space-between; gap:16px;">
                <span style="font-size:0.95rem; font-weight:700; color:#111827; line-height:1.5; flex:1;">{{ $faq->question }}</span>
                <span class="faq-icon" style="flex-shrink:0; width:32px; height:32px; background:#f3f4f6; border-radius:50%; display:flex; align-items:center; justify-content:center; transition:background 0.2s, transform 0.3s;">
                    <svg style="width:16px; height:16px; color:#6b7280; transition:transform 0.3s;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                    </svg>
                </span>
            </button>
            <div class="faq-answer" style="max-height:0; overflow:hidden; transition:max-height 0.35s cubic-bezier(0.4,0,0.2,1);">
                <div style="padding:0 24px 20px; font-size:0.9rem; color:#4b5563; line-height:1.75; border-top:1px solid #f3f4f6;">
                    {!! nl2br(e($faq->answer)) !!}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Bottom CTA --}}
    <div style="text-align:center; margin-top:40px;">
        <p style="font-size:0.9rem; color:#6b7280; margin:0 0 14px;">{{ sc('home', 'faq_cta_text', "Still have questions? We're here to help.") }}</p>
        <a href="{{ route('contact.index') }}"
            style="display:inline-flex; align-items:center; gap:8px; padding:11px 28px; background:#111827; color:#fff; font-size:0.875rem; font-weight:700; border-radius:8px; text-decoration:none; transition:background 0.2s;"
            onmouseover="this.style.background='#000'"
            onmouseout="this.style.background='#111827'">
            <i class="fi fi-rr-envelope" style="line-height:1;"></i>
            {{ sc('home', 'faq_cta_btn', 'Contact Us') }}
        </a>
    </div>

</div>
</section>

<style>
.faq-item:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
.faq-item.open { border-color: #fed7aa; }
.faq-item.open .faq-icon { background: #fff7ed; }
.faq-item.open .faq-icon svg { color: #f97316; transform: rotate(180deg); }
</style>

<script>
function toggleFaq(btn) {
    var item   = btn.closest('.faq-item');
    var answer = item.querySelector('.faq-answer');
    var isOpen = item.classList.contains('open');

    document.querySelectorAll('.faq-item.open').forEach(function(el) {
        el.classList.remove('open');
        el.querySelector('.faq-answer').style.maxHeight = '0';
        el.querySelector('button').setAttribute('aria-expanded', 'false');
    });

    if (!isOpen) {
        item.classList.add('open');
        answer.style.maxHeight = answer.scrollHeight + 'px';
        btn.setAttribute('aria-expanded', 'true');
    }
}
</script>
@endif
