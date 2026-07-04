@include('partials.verification-orders-panel', [
    'verifications' => $verifications,
    'showServerColumn' => $showServerColumn ?? false,
    'panelTitle' => $panelTitle ?? 'Orders',
    'panelLink' => $panelLink ?? url('orders'),
    'panelId' => $panelId ?? 'verification-list-panel',
    'ordersPanelClass' => $ordersPanelClass ?? '',
])
