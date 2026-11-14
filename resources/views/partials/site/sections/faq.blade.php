@php $d = $data ?? []; $items = $d['items'] ?? []; @endphp
<section class="section">
    <div class="container">
        <div class="faq-split">
            <div data-reveal>
                @if (!empty($d['eyebrow']))<span class="eyebrow">{{ $d['eyebrow'] }}</span>@endif
                <h2 class="section-title">{{ $d['heading'] ?? 'Frequently asked questions' }}</h2>
            </div>
            <div class="faq-list" data-reveal data-delay="1">
                @foreach ($items as $item)
                    <div class="faq-item"><button class="faq-q">{{ $item['question'] ?? '' }} <span class="pm" data-icon="plus" data-size="16"></span></button><div class="faq-a"><p>{{ $item['answer'] ?? '' }}</p></div></div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@php
    // FAQPage structured data — emitted only from the Q&A that is visible above.
    $faqEntities = collect($items)
        ->filter(fn ($i) => filled($i['question'] ?? null) && filled($i['answer'] ?? null))
        ->map(fn ($i) => [
            '@type' => 'Question',
            'name' => $i['question'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => strip_tags($i['answer'])],
        ])->values()->all();
@endphp
@if (!empty($faqEntities))
@push('schema')
<script type="application/ld+json">
{!! json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faqEntities], JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush
@endif
