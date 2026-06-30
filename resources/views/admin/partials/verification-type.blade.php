@php
    $labels = [
        1 => ['USA1', 'secondary'],
        2 => ['Legacy', 'secondary'],
        3 => ['5SIM', 'info'],
        4 => ['USA2', 'warning'],
        8 => ['SMSPool', 'primary'],
        9 => ['HeroSMS', 'dark'],
        10 => ['SMS Bower', 'success'],
    ];
    $badge = $labels[$type] ?? ['Unknown', 'secondary'];
@endphp
<span class="badge badge-{{ $badge[1] }}">{{ $badge[0] }}</span>
