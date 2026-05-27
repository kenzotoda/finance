@props([
    'size' => 'md',
    'variant' => 'light',
])

@php
    $sizes = [
        'xs' => ['box' => '28px', 'radius' => '9px', 'icon' => '13px', 'title' => '20px', 'gap' => '8px'],
        'sm' => ['box' => '36px', 'radius' => '12px', 'icon' => '16px', 'title' => '26px', 'gap' => '10px'],
        'md' => ['box' => '46px', 'radius' => '14px', 'icon' => '20px', 'title' => '34px', 'gap' => '12px'],
        'lg' => ['box' => '58px', 'radius' => '18px', 'icon' => '26px', 'title' => '44px', 'gap' => '14px'],
        'xl' => ['box' => '74px', 'radius' => '22px', 'icon' => '34px', 'title' => '58px', 'gap' => '16px'],
    ];

    $style = $sizes[$size] ?? $sizes['md'];
    $isLight = $variant === 'light';
    $boxBackground = $isLight
        ? 'background: linear-gradient(135deg, rgba(255,255,255,0.3), rgba(255,255,255,0.12)); border: 1px solid rgba(255,255,255,0.4);'
        : 'background: linear-gradient(135deg, #2563eb, #0ea5e9); border: 1px solid rgba(59,130,246,0.35);';
    $titleColor = $isLight ? '#ffffff' : '#0f172a';
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center']) }} style="gap: {{ $style['gap'] }};">
    <div style="
        width: {{ $style['box'] }};
        height: {{ $style['box'] }};
        border-radius: {{ $style['radius'] }};
        {{ $boxBackground }}
        display: inline-flex;
        align-items: center;
        justify-content: center;
        overflow: visible;
        box-shadow: 0 14px 30px rgba(2, 6, 23, 0.18);
    ">
        <x-icons.currency-dollar :size="$style['icon']" color="#ffffff" />
    </div>
    <span style="
        color: {{ $titleColor }};
        font-size: {{ $style['title'] }};
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1;
    ">Finance</span>
</div>
