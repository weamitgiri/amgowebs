@php $d = $data ?? []; @endphp
@if (!empty($d['html']))
    <section class="section">
        <div class="container">
            {!! $d['html'] !!}
        </div>
    </section>
@endif
