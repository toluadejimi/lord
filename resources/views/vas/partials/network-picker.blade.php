<div class="mb-4">
    <div class="vas-field-label">Select network</div>
    <input type="hidden" name="service_id" id="network-input" value="{{ old('service_id') }}" required>
    <div class="network-grid" id="network-grid" role="listbox" aria-label="Mobile network">
        @foreach($networks as $network)
        @php
            $id = $network['id'];
            $logoClass = match($id) {
                'mtn' => 'mtn',
                'glo' => 'glo',
                'airtel' => 'airtel',
                '9mobile' => 'n9mobile',
                default => 'mtn',
            };
            $short = match($id) {
                'mtn' => 'MTN',
                'glo' => 'Glo',
                'airtel' => 'AT',
                '9mobile' => '9M',
                default => strtoupper(substr($network['name'], 0, 2)),
            };
            $isSelected = old('service_id') === $id;
        @endphp
        <button type="button"
            class="network-chip {{ $isSelected ? 'selected' : '' }}"
            data-network-id="{{ $id }}"
            role="option"
            aria-selected="{{ $isSelected ? 'true' : 'false' }}">
            <span class="network-logo {{ $logoClass }}">{{ $short }}</span>
            <span>{{ $network['name'] }}</span>
        </button>
        @endforeach
    </div>
    @if(empty($networks))
    <div class="text-muted small mt-2">Networks unavailable. Please try again later.</div>
    @endif
</div>

<script>
(function () {
    const input = document.getElementById('network-input');
    const grid = document.getElementById('network-grid');
    if (!input || !grid) return;

    grid.querySelectorAll('.network-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            grid.querySelectorAll('.network-chip').forEach(function (c) {
                c.classList.remove('selected');
                c.setAttribute('aria-selected', 'false');
            });
            chip.classList.add('selected');
            chip.setAttribute('aria-selected', 'true');
            input.value = chip.dataset.networkId || '';
            input.dispatchEvent(new Event('change', { bubbles: true }));
        });
    });

    if (input.value) {
        const active = grid.querySelector('[data-network-id="' + input.value + '"]');
        if (active) {
            active.classList.add('selected');
            active.setAttribute('aria-selected', 'true');
        }
    }
})();
</script>
