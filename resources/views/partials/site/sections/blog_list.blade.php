@php $d = $data ?? []; $items = $posts ?? collect(); @endphp
<section class="section">
    <div class="container">
        @if (!empty($d['heading']) || !empty($d['eyebrow']))
            <div class="section-head center" data-reveal>
                @if (!empty($d['eyebrow']))<span class="eyebrow eyebrow--violet">{{ $d['eyebrow'] }}</span>@endif
                @if (!empty($d['heading']))<h2 class="section-title">{{ $d['heading'] }}</h2>@endif
            </div>
        @endif
        <div class="blog-grid">
            @foreach ($items as $post)
                <a class="blog-card" href="{{ route('blog.show', $post->slug) }}" data-reveal>
                    <div class="thumb"><span class="cat">{{ $post->category?->name ?? 'Article' }}</span><span data-icon="pen" data-size="40" style="color:var(--cyan);opacity:.4"></span></div>
                    <div class="bc-body"><div class="meta">{{ $post->published_at?->format('M j, Y') }}</div><h3>{{ $post->title }}</h3><p>{{ $post->excerpt }}</p><span class="read">Read <span data-icon="arrow" data-size="15"></span></span></div>
                </a>
            @endforeach
        </div>
    </div>
</section>
