
@if (count($breadcrumbs ?? []))
    @foreach ($breadcrumbs as $breadcrumb)
        <span class="breadcrumb-separator">/</span>
        @if ($breadcrumb['url'] && !$loop->last)
            <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
        @else
            <span class="breadcrumb-current">{{ $breadcrumb['title'] }}</span>
        @endif
    @endforeach
@endif
