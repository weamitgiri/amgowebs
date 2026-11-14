@php
    // $crumbs: array of ['name' => ..., 'url' => ...] in order (Home first).
    $crumbs = $crumbs ?? [];
    $list = [];
    foreach ($crumbs as $i => $c) {
        $item = ['@type' => 'ListItem', 'position' => $i + 1, 'name' => $c['name']];
        if (!empty($c['url'])) {
            $item['item'] = $c['url'];
        }
        $list[] = $item;
    }
@endphp
@if (!empty($list))
@push('schema')
<script type="application/ld+json">
{!! json_encode(['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $list], JSON_UNESCAPED_SLASHES) !!}
</script>
@endpush
@endif
