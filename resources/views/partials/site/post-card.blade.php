@php $card = $post->card_image; @endphp
<a class="blog-card" href="{{ route('blog.show', $post->slug) }}" data-reveal>
    <div class="thumb">
        <span class="cat">{{ $post->category?->name ?? 'Article' }}</span>
        @if ($card)
            <img src="{{ $card }}" alt="{{ $post->image_alt_text }}" loading="lazy" decoding="async" width="640" height="400">
        @else
            <span data-icon="layout" data-size="40" style="color:var(--cyan);opacity:.4"></span>
        @endif
    </div>
    <div class="bc-body">
        <div class="meta">{{ $post->published_at?->format('M j, Y') }} · {{ $post->reading_time }} min read</div>
        <h3>{{ $post->title }}</h3>
        <p>{{ $post->excerpt }}</p>
        <span class="read">Read Article <span data-icon="arrow" data-size="15"></span></span>
    </div>
</a>
