// Form enhancements: basic client-side validation and datalist helpers
document.addEventListener('DOMContentLoaded', function () {
    // Show datalist suggestions when input is focused
    document.querySelectorAll('input[data-datalist]').forEach(function (input) {
        const listId = input.getAttribute('data-datalist');
        const datalist = document.getElementById(listId);
        if (!datalist) return;

        // On focus, show the datalist by setting the value briefly (works in some browsers)
        input.addEventListener('focus', function () {
            // no-op for most browsers; keep as hook for future enhancements
        });
    });

    // Generic client-side validation for inputs with data-validate attribute
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            let formValid = true;
            form.querySelectorAll('[required]').forEach(function (el) {
                const errorEl = document.getElementById(el.id + '-error');
                if (!el.value || (el.type === 'checkbox' && !el.checked)) {
                    formValid = false;
                    if (errorEl) {
                        errorEl.textContent = 'Este campo es requerido';
                        errorEl.style.display = 'block';
                    }
                    el.classList.add('border-red-500');
                } else {
                    if (errorEl) {
                        errorEl.style.display = 'none';
                    }
                    el.classList.remove('border-red-500');
                }
            });

            // If there are invalid elements, prevent submit
            if (!formValid) {
                e.preventDefault();
                const firstInvalid = form.querySelector('.border-red-500');
                if (firstInvalid) firstInvalid.focus();
            }
        });
    });
});
