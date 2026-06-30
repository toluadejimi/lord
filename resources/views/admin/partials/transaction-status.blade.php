@php
    $map = [
        0 => ['Pending', 'secondary'],
        1 => ['Initiated', 'warning'],
        2 => ['Completed', 'success'],
        3 => ['Cancelled', 'danger'],
        4 => ['Resolved', 'info'],
    ];
    $badge = $map[$status] ?? ['Unknown', 'secondary'];
@endphp
<span class="badge badge-pill badge-{{ $badge[1] }}">{{ $badge[0] }}</span>
