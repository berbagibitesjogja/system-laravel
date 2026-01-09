<div {{ $attributes->merge(['class' => 'w-full']) }}>
    <div class="hidden md:block overflow-hidden rounded-xl border border-navy-100 shadow-md">
        <table class="w-full text-sm text-left text-navy-700">
            <thead class="text-xs text-navy-600 uppercase bg-gradient-to-r from-navy-50 to-tosca-50 font-semibold">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-navy-100">
                {{ $body }}
            </tbody>
        </table>
    </div>
    
    <div class="md:hidden space-y-3">
        {{ $mobileBody ?? $body }}
    </div>
</div>
