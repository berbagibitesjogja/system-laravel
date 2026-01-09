<div {{ $attributes->merge(['class' => 'w-full overflow-hidden rounded-xl border border-navy-100 shadow-md']) }}>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-navy-700 min-w-max">
            <thead class="text-xs text-navy-600 uppercase bg-gradient-to-r from-navy-50 to-tosca-50 font-semibold border-b border-navy-100">
                <tr>
                    {{ $head }}
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-navy-100">
                {{ $body }}
            </tbody>
        </table>
    </div>
</div>
