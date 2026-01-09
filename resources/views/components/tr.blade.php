<tr {{ $attributes->merge(['class' => 'odd:bg-white even:bg-gray-50 border-b']) }}>
    {{ $slot }}
</tr>
