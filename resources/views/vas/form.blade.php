@extends('layout.main')
@section('content')
<div class="pc-container">
    <div class="pc-content p-4">
        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="mb-3">
            <a href="{{ route('vas.index') }}" class="text-muted small"><i class="ti ti-arrow-left"></i> Back to Bills &amp; VTU</a>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-start mb-4 gap-3">
            <div class="d-flex align-items-center gap-3">
                <span class="vtu-form-icon"><i class="ti {{ $icon }}"></i></span>
                <div>
                    <h2 class="mb-1">{{ $title }}</h2>
                    <p class="text-muted mb-0 small">{{ $description }}</p>
                    <span class="badge bg-light text-dark border mt-1">Provider: {{ $provider }}</span>
                </div>
            </div>
            <div class="text-end">
                <div class="text-muted small">Wallet</div>
                <div class="h5 mb-0">₦{{ number_format($wallet, 2) }}</div>
            </div>
        </div>

        @if(!$configured)
            <div class="alert alert-warning">
                This service is enabled but not fully configured yet. Please contact admin to set the SprintPay category ID.
            </div>
        @else
            <div class="row justify-content-center">
                <div class="col-lg-7">
                    <form method="post" action="{{ route('vas.purchase') }}" class="card shadow-sm border-0" id="vtu-form">
                        @csrf
                        <input type="hidden" name="category_id" value="{{ $categoryId }}">

                        <div class="card-body p-4">
                            @if($slug === 'airtime')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Phone number</label>
                                    <input class="form-control" name="phone" type="tel" inputmode="numeric"
                                        pattern="[0-9]{11}" maxlength="11" placeholder="e.g. 08012345678" required>
                                    <div class="form-text">Enter the 11-digit Nigerian mobile number to recharge.</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Amount (₦)</label>
                                    <input class="form-control form-control-lg" name="amount" id="amount-input" type="number" min="50" step="1" required>
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @foreach([100, 200, 500, 1000, 2000, 5000] as $preset)
                                        <button type="button" class="btn btn-sm btn-outline-primary amount-preset" data-amount="{{ $preset }}">₦{{ number_format($preset) }}</button>
                                        @endforeach
                                    </div>
                                </div>

                            @elseif($slug === 'data')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Phone number</label>
                                    <input class="form-control" name="phone" type="tel" inputmode="numeric"
                                        pattern="[0-9]{11}" maxlength="11" placeholder="e.g. 08012345678" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Data bundle</label>
                                    @if(empty($variations))
                                        <div class="alert alert-info mb-0">No bundles returned from provider. Check admin category ID or try again later.</div>
                                    @else
                                        <select name="variation_code" id="variation-select" class="form-select" required>
                                            <option value="">Select a bundle</option>
                                            @foreach($variations as $v)
                                            @php
                                                $code = $v['variation_code'] ?? $v['code'] ?? '';
                                                $name = $v['name'] ?? $v['variation_name'] ?? 'Bundle';
                                                $price = $v['variation_amount'] ?? $v['amount'] ?? $v['price'] ?? '';
                                            @endphp
                                            <option value="{{ $code }}" data-amount="{{ $price }}">
                                                {{ $name }}@if($price !== '') — ₦{{ number_format((float) $price, 2) }}@endif
                                            </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Amount (₦)</label>
                                    <input class="form-control form-control-lg" name="amount" id="amount-input" type="number" min="50" step="1" required readonly>
                                </div>

                            @elseif($slug === 'cable')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Smartcard / IUC number</label>
                                    <input class="form-control" name="billersCode" id="billers-code" type="text" placeholder="Enter decoder smartcard number" required>
                                </div>
                                <div class="mb-3">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="validate-bill-btn" data-type="cable">
                                        Validate customer
                                    </button>
                                    <div id="validate-result" class="small mt-2"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Contact phone (optional)</label>
                                    <input class="form-control" name="phone" type="tel" maxlength="11" placeholder="Your phone number">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Amount (₦)</label>
                                    <input class="form-control form-control-lg" name="amount" id="amount-input" type="number" min="50" step="1" required>
                                </div>

                            @elseif($slug === 'electricity')
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Meter number</label>
                                    <input class="form-control" name="billersCode" id="billers-code" type="text" placeholder="Enter prepaid meter number" required>
                                </div>
                                <div class="mb-3">
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="validate-bill-btn" data-type="electricity">
                                        Validate meter
                                    </button>
                                    <div id="validate-result" class="small mt-2"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Contact phone</label>
                                    <input class="form-control" name="phone" type="tel" maxlength="11" placeholder="Phone linked to meter" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Amount (₦)</label>
                                    <input class="form-control form-control-lg" name="amount" id="amount-input" type="number" min="50" step="1" required>
                                    <div class="form-text">Minimum ₦50. Enter the token amount you want to purchase.</div>
                                </div>
                            @endif

                            <button type="submit" class="btn btn-primary btn-lg w-100 mt-2" @if($slug === 'data' && empty($variations)) disabled @endif>
                                Pay ₦<span id="pay-amount-label">0.00</span> from wallet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.vtu-form-icon {
    width: 56px; height: 56px; border-radius: 14px;
    background: linear-gradient(135deg, #6366f1, #8b5cf6);
    color: #fff; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem;
}
</style>

<script>
(function () {
    const amountInput = document.getElementById('amount-input');
    const payLabel = document.getElementById('pay-amount-label');
    const csrf = '{{ csrf_token() }}';
    const categoryId = '{{ $categoryId }}';

    function updatePayLabel() {
        if (!amountInput || !payLabel) return;
        const val = parseFloat(amountInput.value || 0);
        payLabel.textContent = val.toLocaleString('en-NG', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    document.querySelectorAll('.amount-preset').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (amountInput) {
                amountInput.value = btn.dataset.amount;
                updatePayLabel();
            }
        });
    });

    if (amountInput) {
        amountInput.addEventListener('input', updatePayLabel);
        updatePayLabel();
    }

    const variationSelect = document.getElementById('variation-select');
    if (variationSelect && amountInput) {
        variationSelect.addEventListener('change', function () {
            const opt = variationSelect.selectedOptions[0];
            const amount = opt ? opt.dataset.amount : '';
            amountInput.value = amount || '';
            updatePayLabel();
        });
    }

    const validateBtn = document.getElementById('validate-bill-btn');
    const billersInput = document.getElementById('billers-code');
    const validateResult = document.getElementById('validate-result');

    if (validateBtn && billersInput && validateResult) {
        validateBtn.addEventListener('click', function () {
            const code = billersInput.value.trim();
            if (!code) {
                validateResult.innerHTML = '<span class="text-danger">Enter the number first.</span>';
                return;
            }

            validateBtn.disabled = true;
            validateResult.innerHTML = '<span class="text-muted">Validating…</span>';

            const body = new FormData();
            body.append('_token', csrf);
            body.append('category_id', categoryId);
            body.append('billersCode', code);
            body.append('type', validateBtn.dataset.type || 'cable');

            fetch('{{ route('vas.validate-bill') }}', { method: 'POST', body: body })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (data.status) {
                        const name = data.customer_name || data.name || data.data?.customer_name || 'Verified';
                        validateResult.innerHTML = '<span class="text-success"><strong>' + name + '</strong> — ready to pay.</span>';
                    } else {
                        validateResult.innerHTML = '<span class="text-danger">' + (data.message || 'Could not validate. Check the number and try again.') + '</span>';
                    }
                })
                .catch(function () {
                    validateResult.innerHTML = '<span class="text-danger">Validation request failed.</span>';
                })
                .finally(function () {
                    validateBtn.disabled = false;
                });
        });
    }
})();
</script>
@endsection
