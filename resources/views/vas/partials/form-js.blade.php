document.querySelectorAll('.phone-input').forEach(function (input) {
    input.addEventListener('input', function () {
        input.value = input.value.replace(/\D/g, '').slice(0, 11);
    });
});

document.querySelectorAll('.amount-preset').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const amountInput = document.querySelector('.amount-input');
        if (amountInput) amountInput.value = btn.dataset.amount;
    });
});

const vasForm = document.getElementById('vas-form');
if (vasForm) {
    vasForm.addEventListener('submit', function () {
        const btn = vasForm.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing…';
        }
    });
}
