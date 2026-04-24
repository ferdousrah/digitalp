export function initProductFilter() {
    const filterForm = document.querySelector('[data-product-filter]');
    if (!filterForm) return;

    const inputs = filterForm.querySelectorAll('select, input[type="checkbox"]');
    inputs.forEach(input => {
        input.addEventListener('change', () => {
            filterForm.submit();
        });
    });
}
