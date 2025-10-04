@props(['route', 'icon', 'title'])

<a href="{{ $route }}" class="btn btn-primary mb-3" {{ $attributes }}>
    <i class="{{ $icon }}"></i> {{ $title }}
</a>
