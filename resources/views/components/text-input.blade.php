@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-lg border-blue-200 shadow-sm focus:border-blue-500 focus:ring-blue-500']) }}>
