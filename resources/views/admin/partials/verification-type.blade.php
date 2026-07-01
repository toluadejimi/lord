@php
    use App\Support\VerificationLabels;
    $providerName = VerificationLabels::providerName((int) $type);
    $badgeClass = VerificationLabels::providerBadgeClass((int) $type);
@endphp
<span class="badge badge-{{ $badgeClass }}">{{ $providerName }}</span>
